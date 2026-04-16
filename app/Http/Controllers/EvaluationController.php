<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\ResumeScreening;
use App\Models\Evaluation;
use App\Services\GeminiService;

class EvaluationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'type' => 'required|in:HR,Technical,Task_AI,Task_Manual',
            'feedback' => 'nullable|string',
            'score' => 'required|integer|min:1|max:10',
        ]);

        $evaluation = Evaluation::updateOrCreate(
            [
                'candidate_id' => $request->candidate_id,
                'type' => $request->type
            ],
            [
                'feedback' => $request->feedback,
                'score' => $request->score
            ]
        );

        $this->calculateScore($request->candidate_id);

        $candidate = Candidate::with('screening', 'evaluations')->find($request->candidate_id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Evaluation saved.',
                'candidate' => $candidate,
                'evaluation' => $evaluation,
            ]);
        }

        return back()->with('success', 'Evaluation saved.');
    }

    public function generateAiTaskEvaluation(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $candidateId = $request->candidate_id;

        $hrEvaluation = Evaluation::where('candidate_id', $candidateId)->where('type', 'HR')->first();
        $technicalEvaluation = Evaluation::where('candidate_id', $candidateId)->where('type', 'Technical')->first();

        if (!$hrEvaluation) {
            return response()->json(['error' => 'HR Round evaluation must be completed first.'], 400);
        }

        if (!$technicalEvaluation) {
            return response()->json(['error' => 'Technical Round evaluation must be completed first.'], 400);
        }

        if (!$hrEvaluation->feedback || !$hrEvaluation->score) {
            return response()->json(['error' => 'HR Round must have both feedback and score.'], 400);
        }

        if (!$technicalEvaluation->feedback || !$technicalEvaluation->score) {
            return response()->json(['error' => 'Technical Round must have both feedback and score.'], 400);
        }

        try {
            $geminiService = new GeminiService();
            $aiResult = $geminiService->generateTaskEvaluation(
                $hrEvaluation->feedback,
                $hrEvaluation->score,
                $technicalEvaluation->feedback,
                $technicalEvaluation->score
            );

            if (!$aiResult) {
                return response()->json([
                    'error' => 'AI service is temporarily unavailable. Check your Gemini API key and try again.'
                ], 500);
            }

            $evaluation = Evaluation::updateOrCreate(
                [
                    'candidate_id' => $candidateId,
                    'type' => 'Task_AI'
                ],
                [
                    'feedback' => $aiResult['feedback'],
                    'score' => $aiResult['score']
                ]
            );

            $this->calculateScore($candidateId);

            $candidate = Candidate::with('screening', 'evaluations')->find($candidateId);

            return response()->json([
                'message' => '✅ AI Task evaluation generated successfully!',
                'evaluation' => $evaluation,
                'candidate' => $candidate,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Evaluation Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateScore($candidateId)
    {
        $evaluations = Evaluation::where('candidate_id', $candidateId)->pluck('score');

        if ($evaluations->count() < 3) {
            return;
        }

        $avg = $evaluations->avg();

        $candidate = Candidate::find($candidateId);
        $candidate->overall_score = $avg;

        if ($avg >= 6) {
            $candidate->final_status = 'Selected';
        } else {
            $candidate->final_status = 'Rejected';
        }

        $candidate->save();
    }
}