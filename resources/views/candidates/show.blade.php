@extends('layouts.app')

@section('content')

<h3>{{ $candidate->name }}</h3>

<div id="ajax-status"></div>

<div class="card p-3 mb-3">
    <p><b>Email:</b> {{ $candidate->email }}</p>
    <p><b>Phone:</b> {{ $candidate->phone }}</p>
    <p><b>Resume:</b>
        @if($candidate->resume)
            <a href="{{ route('candidates.resume', $candidate) }}" target="_blank">View</a>
        @else
            <span>No resume uploaded</span>
        @endif
    </p>
    <p><b>Screening Status:</b> <span id="screening-status">{{ $candidate->screening->status ?? 'Pending' }}</span></p>
    <p><b>Overall Score:</b> <span id="overall-score">{{ $candidate->overall_score ?? '-' }}</span></p>
    <p><b>Final Status:</b> <span id="final-status">{{ $candidate->final_status ?? '-' }}</span></p>
</div>

<h4>Resume Screening</h4>

<form class="ajax-form" method="POST" action="{{ route($routePrefix . 'screening.update') }}">
    @csrf
    <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">

    <select name="status" class="form-control mb-2">
        <option{{ optional($candidate->screening)->status == 'Pending' ? ' selected' : '' }}>Pending</option>
        <option{{ optional($candidate->screening)->status == 'Shortlisted' ? ' selected' : '' }}>Shortlisted</option>
        <option{{ optional($candidate->screening)->status == 'Rejected' ? ' selected' : '' }}>Rejected</option>
    </select>

    <textarea name="remarks" class="form-control mb-2" placeholder="Remarks">{{ optional($candidate->screening)->remarks }}</textarea>

    <button class="btn btn-primary">Update</button>
</form>

<h4 class="mt-4">Evaluation</h4>

@include('evaluations.form', ['type' => 'HR', 'action' => route($routePrefix . 'evaluation.store')])
@include('evaluations.form', ['type' => 'Technical', 'action' => route($routePrefix . 'evaluation.store')])
@include('evaluations.form', ['type' => 'Task_Manual', 'action' => route($routePrefix . 'evaluation.store')])
@include('evaluations.form', ['type' => 'Task_AI', 'action' => route($routePrefix . 'evaluation.store')])

<div class="mb-3">
    <button id="generate-ai-task" class="btn btn-primary">Generate AI Task Evaluation</button>
    <div id="ai-loader" style="display: none;">Generating AI evaluation...</div>
</div>
<ul id="evaluation-list" class="list-group">
    @forelse($candidate->evaluations as $evaluation)
        <li class="list-group-item">
            <strong>{{ $evaluation->type }}:</strong>
            Score {{ $evaluation->score ?? 'N/A' }}
            <div>{{ $evaluation->feedback }}</div>
        </li>
    @empty
        <li class="list-group-item">No evaluations yet.</li>
    @endforelse
</ul>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.ajax-form');
        const status = document.getElementById('ajax-status');
        const overallScore = document.getElementById('overall-score');
        const finalStatus = document.getElementById('final-status');
        const screeningStatus = document.getElementById('screening-status');
        const evaluationList = document.getElementById('evaluation-list');
        const generateAiButton = document.getElementById('generate-ai-task');
        const aiLoader = document.getElementById('ai-loader');

        function showStatus(message, type = 'success') {
            status.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        }

        function renderEvaluations(evaluations) {
            if (!evaluations || evaluations.length === 0) {
                evaluationList.innerHTML = '<li class="list-group-item">No evaluations yet.</li>';
                return;
            }

            evaluationList.innerHTML = evaluations.map(ev => `
                <li class="list-group-item">
                    <strong>${ev.type}:</strong>
                    Score ${ev.score ?? 'N/A'}
                    <div>${ev.feedback || ''}</div>
                </li>
            `).join('');
        }

        forms.forEach(form => {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                status.innerHTML = '';
                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: formData,
                    });
                    const json = await response.json();

                    if (!response.ok) {
                        throw json;
                    }

                    showStatus(json.message || 'Saved successfully.');

                    if (json.candidate) {
                        overallScore.textContent = json.candidate.overall_score ?? '-';
                        finalStatus.textContent = json.candidate.final_status ?? '-';
                        screeningStatus.textContent = json.candidate.screening?.status ?? 'Pending';
                        renderEvaluations(json.candidate.evaluations || []);
                    }
                } catch (error) {
                    let html = '<div class="alert alert-danger"><ul class="mb-0">';
                    if (error.errors) {
                        Object.values(error.errors).forEach(errList => {
                            errList.forEach(err => {
                                html += `<li>${err}</li>`;
                            });
                        });
                    } else if (error.message) {
                        html += `<li>${error.message}</li>`;
                    } else {
                        html += '<li>Unable to submit request.</li>';
                    }
                    html += '</ul></div>';
                    status.innerHTML = html;
                }
            });
        });

        generateAiButton.addEventListener('click', async function () {
            generateAiButton.disabled = true;
            generateAiButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Generating...';
            aiLoader.style.display = 'block';
            status.innerHTML = '<div class="alert alert-info">Processing AI evaluation... Please wait as long as needed.</div>';

            try {
                const response = await fetch('{{ route("evaluation.generate.ai.task") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ candidate_id: {{ $candidate->id }} }),
                });
                const json = await response.json();

                if (!response.ok) {
                    throw json;
                }

                showStatus((json.message || 'AI evaluation generated successfully!'), 'success');

                if (json.candidate) {
                    overallScore.textContent = json.candidate.overall_score ?? '-';
                    finalStatus.textContent = json.candidate.final_status ?? '-';
                    screeningStatus.textContent = json.candidate.screening?.status ?? 'Pending';
                    renderEvaluations(json.candidate.evaluations || []);
                }
            } catch (error) {
                let errorMsg = 'Unable to generate AI evaluation.';
                
                if (error.error) {
                    errorMsg = error.error;
                } else if (error.message) {
                    errorMsg = error.message;
                }
                
                let html = '<div class="alert alert-danger"><strong>Error:</strong><ul class="mb-0"><li>' + errorMsg + '</li>';
                
                if (error.errors) {
                    Object.values(error.errors).forEach(errList => {
                        errList.forEach(err => {
                            html += `<li>${err}</li>`;
                        });
                    });
                }
                
                html += '</ul></div>';
                status.innerHTML = html;
            } finally {
                aiLoader.style.display = 'none';
                generateAiButton.disabled = false;
                generateAiButton.innerHTML = 'Generate AI Task Evaluation';
            }
        });
    });
</script>
@endpush