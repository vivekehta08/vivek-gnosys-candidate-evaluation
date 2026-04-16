<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;


class DashboardController extends Controller
{
    public function index()
    {
        $candidates = Candidate::with('screening', 'evaluations')->get();

        $summary = [
            'total' => $candidates->count(),
            'selected' => $candidates->where('final_status', 'Selected')->count(),
            'rejected' => $candidates->where('final_status', 'Rejected')->count(),
            'pending' => $candidates->whereNull('final_status')->count(),
        ];

        return view('dashboard.index', compact('candidates', 'summary'));
    }
}
