<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Candidate;
use App\Models\ResumeScreening;
use App\Models\Evaluation;

class CandidateController extends Controller
{
    protected function routePrefix()
    {
        return auth()->user()->role === 'Admin' ? 'admin.' : 'hr.';
    }

    public function index()
    {
        $candidates = Candidate::with('screening','evaluations')->get();
        $routePrefix = $this->routePrefix();
        return view('candidates.index', compact('candidates', 'routePrefix'));
    }

    public function create()
    {
        $routePrefix = $this->routePrefix();
        return view('candidates.create', compact('routePrefix'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:candidates',
            'phone'=>'required',
            'resume'=>'required|mimes:pdf,doc,docx'
        ]);

        $file = $request->file('resume')->store('resumes', 'public');

        $candidate = Candidate::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'resume'=>$file
        ]);

        ResumeScreening::create([
            'candidate_id' => $candidate->id
        ]);

        $candidate->load('screening', 'evaluations');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Candidate created successfully.',
                'candidate' => $candidate,
            ], 201);
        }

        return redirect()->route($this->routePrefix() . 'candidates.index');
    }

    public function show($id)
    {
        $candidate = Candidate::with('screening','evaluations')->findOrFail($id);
        $routePrefix = $this->routePrefix();
        return view('candidates.show', compact('candidate', 'routePrefix'));
    }

    public function downloadResume(Candidate $candidate)
    {
        if (! $candidate->resume) {
            abort(404, 'Resume not found');
        }

        if (Storage::disk('public')->exists($candidate->resume)) {
            return response()->file(Storage::disk('public')->path($candidate->resume));
        }

        if (Storage::exists($candidate->resume)) {
            return response()->file(storage_path('app/' . $candidate->resume));
        }

        abort(404, 'Resume not found');
    }

    public function edit($id)
    {
        $candidate = Candidate::findOrFail($id);
        return view('candidates.edit', compact('candidate'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:candidates,email,' . $id,
            'phone' => 'required',
            'resume' => 'nullable|mimes:pdf,doc,docx'
        ]);

        $candidate = Candidate::findOrFail($id);
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('resume')) {
            $data['resume'] = $request->file('resume')->store('resumes', 'public');
        }

        $candidate->update($data);

        return redirect()->route('admin.candidates.index');
    }

    public function destroy($id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->delete();
        return redirect()->route('admin.candidates.index');
    }
}
