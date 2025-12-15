@extends('layout')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="page-title-section">
                <h2><i class="fas fa-user me-2" style="color: #3b82f6;">
                    </i>Profile</h2>
                <p class="page-subtitle">Manage your info</p>
            </div>
        </div>
    </div>
    <!-- Employee Form Card -->
    <div class="card shadow-sm rounded-4">
        <div class="card-header">
            <h5>Personal Information</h5>
        </div>
        <div class="card-body p-2">
            <form id="updateProfileForm" action="{{ route('profile.update', $profileData->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <!-- Employee ID -->
                <div class="mb-2">
                    <label for="id" class="form-label fw-semibold">ID</label>
                    <input type="text" class="form-control" id="id" name="id" value="{{ $profileData->id }}"
                        readonly>
                </div>

                <!-- Username & Email -->
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            value="{{ $profileData->username }}">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ $profileData->email }}" readonly>
                    </div>
                </div>

                <!-- Address & Profile Image -->
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="address" class="form-label fw-semibold">Address</label>
                        <input type="text" class="form-control" id="address" name="address"
                            value="{{ $profileData->detail->address }}">
                    </div>
                    <div class="col-md-6">
                        <label for="profile_image" class="form-label fw-semibold">Profile Photo</label>
                        <div class="mb-2">
                            <input class="form-control" type="file" id="profile_image" name="profile_image">
                        </div>
                        @if ($profileData->detail->profile_image)
                            <div class="mt-2" id="imagePreviewContainer">
                                <img id="imagePreview" src="{{ asset('storage/' . $profileData->detail->profile_image) }}"
                                    alt="Profile Preview" class="img-thumbnail" style="max-width: 200px;">
                                <input type="hidden" id="current_image" name="current_image"
                                    value="{{ $profileData->detail->profile_image }}">
                            </div>
                        @else
                            <div class="mt-2" id="imagePreviewContainer" style="display: none;">
                                <img id="imagePreview" src="" alt="Profile Preview" class="img-thumbnail"
                                    style="max-width: 200px;">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Phone, DOB, Role -->
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Contact Number</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            value="{{ $profileData->detail->phone }}">
                    </div>
                    <div class="col-md-3">
                        <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                        <input type="text" class="form-control datepicker" id="dob" name="dob"
                            value="{{ $profileData->detail->dob }}">
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label fw-semibold">Role</label>
                        <select id="role" name="role" class="form-select" disabled>
                            <option value="admin" {{ $profileData->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ $profileData->role == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i>Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $("#profile_image").on("change", function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $("#imagePreview").attr("src", e.target.result)
                        $("#imagePreviewContainer").show();
                    }
                    reader.readAsDataURL(file);
                } else {
                    $("#imagePreview").attr("src", '');
                    $("#imagePreviewContainer").hide();
                }
            });
            $(document).on('submit', '#updateProfileForm', function(e) {
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
                            confirmButtonText: 'OK',
                        }).then(() => {
                            window.location.href =
                                "{{ route('dashboard') }}"
                        });
                    },
                    error: function(xhr) {
                        reenableFormButtons('updateProfileForm');
                        if (xhr.status === 422) handleValidationErrors(xhr,
                            '#updateProfileForm');
                        else {
                            alert('Something went wrong');
                            console.error('Error:' + xhr.responseText);
                        }
                    }
                });

            });
        });
    </script>
@endpush
