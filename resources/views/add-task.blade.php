@extends('layout')

@section('content')
    <form action="{{ route('task.store') }}" method="POST" id="addTaskForm">
        @csrf
        <div class="row g-3">

            <div class="col-md-6">
                <div class="form-group">
                    <label for="employee_id">Id</label>
                    <input type="text" class="form-control" value="{{ $employee->id }}" id="employee_id" name="employee_id"
                        required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" value="{{ $employee->username }}" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Task Title</label>
                    <input type="text" class="form-control" id="title" name="title">
                </div>
                <div class="form-group">
                    <label for="start">Date</label>
                    <input type="date" class="form-control" id="start" name="start" min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label for="isImportant">Priority</label>
                    <select name="isImportant" id="isImportant" class="form-control">
                        <option value="0">Normal</option>
                        <option value="1">Important</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mt-3 g-2">
            <div class="col-md-6">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary w-100">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-save me-1"></i> Save Info
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('submit', '#addTaskForm', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('end', $('#start').val());

                $.ajax({
                    url: $(this).attr('action'),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            title: response.success ? 'Success' : 'Error',
                            text: response.message,
                            icon: response.success ? 'success' : 'error',
                        });
                        if (response.success) window.location.href =
                            "{{ route('employees.index') }}";
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) handleValidationErrors(xhr, '#addTaskForm');
                        else console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush
