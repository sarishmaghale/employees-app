@extends('layout')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="page-title-section">
                <h2><i class="fas fa-users me-2" style="color: #3b82f6;">
                    </i>Employees Catalog</h2>
                <p class="page-subtitle">Manage your Staff</p>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-primary btn-add">
                <i class="fas fa-arrow-left me-1"></i>Go Back
            </a>
        </div>
    </div>
    <!-- Employee Form Card -->
    <div class="card shadow-sm rounded-4">
        <div class="card-header">
            <h5>Employee Information</h5>
        </div>
        <div class="card-body p-2">
            <form id="updateEmployeeForm" action="{{ route('employees.update', $employee->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <!-- Employee ID -->
                <div class="mb-2">
                    <label for="id" class="form-label fw-semibold">Employee ID</label>
                    <input type="text" class="form-control" id="id" name="id" value="{{ $employee->id }}"
                        readonly>
                </div>

                <!-- Username & Email -->
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            value="{{ $employee->username }}">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ $employee->email }}">
                    </div>
                </div>

                <!-- Address & Profile Image -->
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="address" class="form-label fw-semibold">Address</label>
                        <input type="text" class="form-control" id="address" name="address"
                            value="{{ $employee->detail->address }}">
                    </div>
                    <div class="col-md-2">
                        <label for="profile_image" class="form-label fw-semibold">Profile Photo</label>
                        @if ($employee->detail->profile_image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $employee->detail->profile_image) }}" alt="Profile Preview"
                                    class="img-thumbnail" style="max-width: 200px;">
                                <input type="hidden" id="current_image" name="current_image"
                                    value="{{ $employee->profile_image }}">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Phone, DOB, Role -->
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Contact Number</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            value="{{ $employee->detail->phone }}">
                    </div>
                    <div class="col-md-3">
                        <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob"
                            value="{{ $employee->detail->dob }}">
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label fw-semibold">Role</label>
                        <select id="role" name="role" class="form-select">
                            <option value="admin" {{ $employee->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ $employee->role == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i>Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('submit', '#updateEmployeeForm', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const editUrl = $(this).attr("action");
            $.ajax({
                url: editUrl,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        title: response.success ? 'Updated' : 'Error',
                        text: response.message,
                        icon: response.success ? 'success' : 'error',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "{{ route('employees.index') }}";

                    });
                },
                error: function(xhr) {
                    alert('Something went wrong');
                    console.error('Error:' + xhr.responseText);
                }
            })
        })
    </script>
@endsection
