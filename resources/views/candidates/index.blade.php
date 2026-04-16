@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between mb-3 align-items-center">
    <h3>Candidate List</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCandidateModal">Add Candidate</button>
</div>

<div id="ajax-status"></div>

<table class="table table-bordered" id="candidate-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Stage</th>
            <th>Score</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($candidates as $c)
        <tr data-id="{{ $c->id }}">
            <td>{{ $c->name }}</td>
            <td>{{ $c->email }}</td>
            <td>{{ $c->current_stage }}</td>
            <td>{{ $c->overall_score ?? '-' }}</td>
            <td>{{ $c->final_status ?? '-' }}</td>
            <td>
                <a href="{{ route($routePrefix . 'candidates.show', $c->id) }}" class="btn btn-info btn-sm">View</a>
                @if(auth()->user()->role === 'Admin')
                    <a href="{{ route('admin.candidates.edit', $c->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('admin.candidates.destroy', $c->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete candidate?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="modal fade" id="createCandidateModal" tabindex="-1" aria-labelledby="createCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCandidateModalLabel">Add Candidate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="candidate-create-form" action="{{ route($routePrefix . 'candidates.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Resume</label>
                        <input type="file" name="resume" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('candidate-create-form');
        const ajaxStatus = document.getElementById('ajax-status');
        const tableBody = document.querySelector('#candidate-table tbody');
        const modal = new bootstrap.Modal(document.getElementById('createCandidateModal'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const userRole = @json(auth()->user()->role);
        const showUrlTemplate = "{{ route($routePrefix . 'candidates.show', ['candidate' => ':id']) }}";
        const editUrlTemplate = "{{ route('admin.candidates.edit', ['candidate' => ':id']) }}";
        const deleteUrlTemplate = "{{ route('admin.candidates.destroy', ['candidate' => ':id']) }}";

        function buildActions(candidate) {
            const viewUrl = showUrlTemplate.replace(':id', candidate.id);
            let actions = `<a href="${viewUrl}" class="btn btn-info btn-sm">View</a>`;

            if (userRole === 'Admin') {
                const editUrl = editUrlTemplate.replace(':id', candidate.id);
                const deleteUrl = deleteUrlTemplate.replace(':id', candidate.id);

                actions += ` <a href="${editUrl}" class="btn btn-warning btn-sm">Edit</a>`;
                actions += ` <form action="${deleteUrl}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete candidate?');">`;
                actions += `<input type="hidden" name="_token" value="${csrfToken}">`;
                actions += `<input type="hidden" name="_method" value="DELETE">`;
                actions += `<button class="btn btn-danger btn-sm">Delete</button></form>`;
            }

            return actions;
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            ajaxStatus.innerHTML = '';

            const formData = new FormData(form);
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                });

                const json = await response.json();
                if (!response.ok) {
                    throw json;
                }

                const candidate = json.candidate;
                const stage = candidate.screening?.status ? (candidate.final_status ? 'Final: ' + candidate.final_status : candidate.screening.status) : 'Screening';
                const score = candidate.overall_score ?? '-';
                const status = candidate.final_status ?? '-';
                const actions = buildActions(candidate);

                const newRow = document.createElement('tr');
                newRow.setAttribute('data-id', candidate.id);
                newRow.innerHTML = `
                    <td>${candidate.name}</td>
                    <td>${candidate.email}</td>
                    <td>${stage}</td>
                    <td>${score}</td>
                    <td>${status}</td>
                    <td>${actions}</td>
                `;
                tableBody.prepend(newRow);

                ajaxStatus.innerHTML = `<div class="alert alert-success">${json.message}</div>`;
                form.reset();
                modal.hide();
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
                    html += '<li>Unable to create candidate.</li>';
                }
                html += '</ul></div>';
                ajaxStatus.innerHTML = html;
            }
        });
    });
</script>
@endpush