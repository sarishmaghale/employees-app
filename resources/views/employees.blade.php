@extends('layout')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="page-title-section">
                <h2><i class="fas fa-users me-2" style="color: #3b82f6;">
                    </i>Employees Catalog</h2>
                <p class="page-subtitle">Manage your Staff</p>
            </div>
            <button type="button" class="btn btn-primary btn-add" id="openEmployeeModal">
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

    @include('partial-views.add-employee-partial')
    @include('partial-views.add-event-partial')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadEmployees();

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
                            const addTaskUrl = '{{ route('employees.task', ':id') }}'.replace(
                                ':id', emp.id);
                            let actions = `
                                  <a href="javascript:void(0);" class="btn btn-secondry btn-action add-task-btn"
                                  data-employee-id="${emp.id}">Add Task</a>
                                <a href="${editUrl}" class="btn btn-warning btn-action">Edit</a>
                                <button class="btn btn-danger btn-delete btn-action delete-employee" data-id="${emp.id}">Delete</button>
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

            $(document).on('click', '#openEmployeeModal', function() {
                $('#newEmployeeModal').modal('show');

            })

            document.addEventListener('employees:refresh', function() {
                loadEmployees();
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

            $(document).on('click', '.add-task-btn', function() {
                const employeeId = $(this).data('employee-id');
                $('#addTaskForm #employee_id').val(employeeId);
                $('#addTaskModal').modal('show');
            })
        });
    </script>
@endpush
