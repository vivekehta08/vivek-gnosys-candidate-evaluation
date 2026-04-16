@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Congratulations! You have advanced to the next round
                </div>

                <div class="card-body">
                    <p>Dear {{ $candidate->name }},</p>

                    <p>Congratulations! You have successfully passed the current round and have been selected for the next round: <strong>{{ $nextRound }}</strong>.</p>

                    <p>Please check your dashboard for more details and next steps.</p>

                    <p>Best regards,<br>
                    {{ config('app.name') }} Team</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection