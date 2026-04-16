@extends('layouts.app')

@section('content')

<h3>Edit Candidate</h3>

<form method="POST" action="{{ route('admin.candidates.update', $candidate->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $candidate->name) }}">
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $candidate->email) }}">
    </div>

    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $candidate->phone) }}">
    </div>

    <div class="mb-3">
        <label>Resume</label>
        <input type="file" name="resume" class="form-control">
        @if ($candidate->resume)
            <p class="mt-2">Current resume: <a href="{{ route('candidates.resume', $candidate) }}" target="_blank">View</a></p>
        @endif
    </div>

    <button class="btn btn-primary">Update</button>
</form>

@endsection