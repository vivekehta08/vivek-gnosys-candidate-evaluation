@extends('layouts.app')

@section('content')

<h3>Dashboard</h3>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Candidates</h5>
                <p class="card-text fs-3">{{ $summary['total'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Selected</h5>
                <p class="card-text fs-3">{{ $summary['selected'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title">Rejected</h5>
                <p class="card-text fs-3">{{ $summary['rejected'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-secondary mb-3">
            <div class="card-body">
                <h5 class="card-title">Pending</h5>
                <p class="card-text fs-3">{{ $summary['pending'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="mb-3">Candidate Progress</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Stage</th>
                    <th>Overall Score</th>
                    <th>Final Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($candidates as $candidate)
                    <tr>
                        <td>{{ $candidate->name }}</td>
                        <td>{{ $candidate->current_stage }}</td>
                        <td>{{ $candidate->overall_score ?? '-' }}</td>
                        <td>{{ $candidate->final_status ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection