<form method="POST" action="{{ $action }}" class="card p-3 mb-3 ajax-form">
    @csrf

    <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
    <input type="hidden" name="type" value="{{ $type }}">

    <h5>{{ $type }} Round</h5>

    <textarea name="feedback" class="form-control mb-2" placeholder="Feedback">{{ old('feedback') }}</textarea>

    <input type="number" name="score" min="1" max="10" class="form-control mb-2" placeholder="Score" value="{{ old('score') }}">

    <button class="btn btn-success">Save</button>
</form>