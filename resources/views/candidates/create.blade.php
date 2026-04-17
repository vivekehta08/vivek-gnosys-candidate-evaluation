@extends('layouts.app')

@section('content')

<h3>Add Candidate</h3>

<div id="ajax-status"></div>

<form id="candidate-form" method="POST" action="{{ route($routePrefix . 'candidates.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
    </div>

    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
    </div>

    <div class="mb-3">
        <label>Resume</label>
        <input type="file" name="resume" class="form-control">
    </div>

    <button class="btn btn-success">Save</button>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('candidate-form');
        const status = document.getElementById('ajax-status');
        const saveButton = form.querySelector('button');

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            status.innerHTML = '';
            saveButton.disabled = true;

            const formData = new FormData(form);
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const json = await response.json();

                if (!response.ok) {
                    throw json;
                }

                form.reset();
                status.innerHTML = `<div class="alert alert-success">${json.message}</div>`;
                saveButton.disabled = false;
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
                    html += '<li>Unable to save candidate.</li>';
                }
                html += '</ul></div>';
                status.innerHTML = html;
                saveButton.disabled = false;
            }
        });
    });
</script>
@endpush