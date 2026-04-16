<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\ResumeScreening;
use Illuminate\Support\Facades\Mail;
use App\Mail\CandidateSelectedMail;
use App\Mail\CandidateRejectedMail;
use App\Mail\CandidateShortlistedMail;

class CandidateApiController extends Controller
{
    public function index()
    {
        return Candidate::with('screening', 'evaluations')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates',
            'phone' => 'required|string|max:30',
            'resume' => 'nullable|string',
        ]);

        $candidate = Candidate::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'resume' => $data['resume'] ?? null,
        ]);

        ResumeScreening::create([
            'candidate_id' => $candidate->id,
        ]);

        return response()->json($candidate->load('screening'), 201);
    }

    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:candidates,id',
            'status' => 'required|in:Pending,Shortlisted,Rejected,Selected',
        ]);

        $candidate = Candidate::findOrFail($data['id']);
        $candidate->final_status = $data['status'];
        $candidate->save();

        if ($data['status'] === 'Shortlisted') {
            Mail::to($candidate->email)->send(new CandidateShortlistedMail($candidate));
        }

        if ($data['status'] === 'Selected') {
            Mail::to($candidate->email)->send(new CandidateSelectedMail($candidate));
        }

        if ($data['status'] === 'Rejected') {
            Mail::to($candidate->email)->send(new CandidateRejectedMail($candidate));
        }

        if ($candidate->screening) {
            $candidate->screening->update([
                'status' => $data['status'] === 'Selected' ? 'Shortlisted' : $data['status'],
            ]);
        }

        return response()->json(['message' => 'Status updated', 'candidate' => $candidate]);
    }
}
