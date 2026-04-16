<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResumeScreening;
use App\Models\Candidate;

class ResumeScreeningController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'status' => 'required|in:Pending,Shortlisted,Rejected',
        ]);

        $screening = ResumeScreening::updateOrCreate(
            ['candidate_id' => $request->candidate_id],
            [
                'status' => $request->status,
                'remarks' => $request->remarks
            ]
        );

        $candidate = Candidate::find($request->candidate_id);

        if ($request->status === 'Rejected') {
            $candidate->final_status = 'Rejected';
            $candidate->save();
        } elseif ($request->status === 'Pending') {
            $candidate->final_status = null;
            $candidate->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Screening updated successfully.',
                'screening' => $screening,
                'candidate' => $candidate,
            ]);
        }

        return back()->with('success', 'Screening Updated');
    }
}
