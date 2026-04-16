@extends('layouts.app')

@section('content')
<div class="text-center py-5">
    <div class="display-1 text-danger">403</div>
    <div class="mb-4 fs-4">Forbidden</div>
    <p class="lead">You do not have permission to access this page.</p>
    <a href="{{ url()->previous() ?: route('login') }}" class="btn btn-primary">Go Back</a>
</div>
@endsection
