@extends('layout')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="page-title-section">
                <h2><i class="fas fa-users me-2" style="color: #3b82f6;">
                    </i>Employees Catalog</h2>
                <p class="page-subtitle">Manage your Staff</p>
            </div>
            <button type="button" class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#newEmployeeModal">
                <i class="fas fa-plus me-2"></i>Add Employee
            </button>
        </div>
    </div>

    <div class="table-wrapper">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="employeesTableBody">

                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="newEmployeeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Add New Employee
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('employees.store') }}" method="POST" id="newEmployeeForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="text" class="form-control" id="password" name="password">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-row">
                                    <div class="form-group col-md 3">
                                        <label>DOB</label>
                                        <input type="date" class="form-control" id="dob" name="dob">
                                    </div>
                                    <div class="form-group col-md 3">
                                        <label>Address</label>
                                        <input type="text" class="form-control" placeholder="Address" id="address"
                                            name="address">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Role</label>
                                    <select class="form-control" id="role" name="role">
                                        <option selected disabled>Select Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="staff">Staff</option>
                                    </select>
                                </div>

                            </div>
                            <div class="form-group ">
                                <label>Contact info</label>
                                <input type="text" class="form-control" placeholder="contact" id="phone"
                                    name="phone">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Info
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadEmployees();

            $('#newEmployeeModal').on('show.bs.modal', function() {
                document.getElementById('mainContent').setAttribute('inert', '');
            });
            $('#newEmployeeModal').on('hidden.bs.modal', function() {
                document.getElementById('mainContent').removeAttribute('inert');
            });


            //  get employees list to populate table
            function loadEmployees() {
                $("#employeesTableBody").empty();
                $.ajax({
                    url: `{{ route('employees.list') }}`,
                    type: "GET",
                    dataType: "json",
                    success: function(employees) {
                        let rows = "";
                        employees.forEach(emp => {
                            let tr = $("<tr>");
                            tr.append($("<td>").text(emp.id));
                            tr.append($("<td>").text(emp.username));
                            tr.append($("<td>").text(emp.email));
                            tr.append($("<td>").text(emp.role));
                            tr.append($("<td>").text(emp.detail.phone));
                            const editUrl = '{{ route('employees.show', ':id') }}'.replace(
                                ':id', emp.id);
                            let actions = `
                                <a href="${editUrl}" class="btn btn-edit btn-action">Edit</a>
                                <button class="btn btn-delete btn-action delete-employee" data-id="${emp.id}">Delete</button>
                                `;
                            tr.append($("<td>").html(actions));
                            $("#employeesTableBody").append(tr)
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", "Something went wrong", "error");
                        console.error('Error:' + xhr.responseText);
                    }
                });
            }

            // handle new employee form submission
            $(document).on("submit", "#newEmployeeForm", function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const createUrl = $(this).attr("action");

                $.ajax({
                    url: createUrl,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        $('#newEmployeeModal').modal('hide');
                        Swal.fire({
                            title: response.success ? 'Success' : 'Error',
                            text: response.message,
                            icon: response.success ? 'success' : 'error',
                            confirmButtonText: 'OK,'
                        });
                        if (response.success) {
                            loadEmployees();
                        }

                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            alert('Please fill all required fields correctly.');
                            return;
                        }
                        alert('Something went wrong');
                        console.error('Error:' + xhr.responseText);
                    }
                });
            });

            // delete Employee
            $(document).on('click', '.delete-employee', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const deleteUrl = `{{ route('employees.delete', ':id') }}`.replace(':id', id);
                Swal.fire({
                    title: 'Are you sure',
                    text: 'This employee will be deleted',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: "PATCH",
                            data: {
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: response.success ? 'Deleted!' :
                                        'Error',
                                    text: response.message,
                                    icon: response.success ? 'success' :
                                        'error',
                                    confirmButtonText: 'OK',
                                });
                                loadEmployees();
                            },
                            error: function(xhr) {
                                alert('Something went wrong');
                                console.error('Error:' + xhr.responseText);
                            }
                        })
                    }
                });
            });

        });
    </script>
@endpush
