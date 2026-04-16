<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $timeout = 15;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function generateTaskEvaluation($hrFeedback, $hrScore, $technicalFeedback, $technicalScore)
    {
        if (!$this->apiKey) {
            Log::error('Gemini API Key not found in .env');
            return ['error' => 'API key not configured'];
        }

        $prompt = "Based on:\nHR: {$hrFeedback} (Score: {$hrScore}/10)\nTech: {$technicalFeedback} (Score: {$technicalScore}/10)\n\nGenerate JSON:\n{\"feedback\":\"analysis here\",\"score\":7}";

        try {
            Log::info('Gemini API Call Started', [
                'api_key_set' => !empty($this->apiKey),
                'timeout' => $this->timeout
            ]);

            $response = Http::withoutVerifying()
                ->timeout($this->timeout)
                ->connectTimeout(5)
                ->withHeaders([
                    'X-goog-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent',
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ]
                    ]
                );

            Log::info('Gemini API Response', [
                'status' => $response->status(),
                'body_length' => strlen($response->body())
            ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error Response', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500)
                ]);
                return null;
            }

            $data = $response->json();
            
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('Invalid response structure', ['data' => json_encode($data)]);
                return null;
            }

            $text = $data['candidates'][0]['content']['parts'][0]['text'];
            
            preg_match('/\{[^}]+\}/', $text, $matches);
            
            if (empty($matches)) {
                Log::error('No JSON found in response', ['text' => $text]);
                return null;
            }

            $parsed = json_decode($matches[0], true);

            if (!$parsed || !isset($parsed['feedback']) || !isset($parsed['score'])) {
                Log::error('JSON parse failed', ['parsed' => $parsed]);
                return null;
            }

            $score = intval($parsed['score']);
            if ($score < 1) $score = 1;
            if ($score > 10) $score = 10;

            Log::info('Gemini API Success', [
                'feedback_length' => strlen($parsed['feedback']),
                'score' => $score
            ]);

            return [
                'feedback' => trim($parsed['feedback']),
                'score' => $score
            ];

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return null;
        }
    }
}