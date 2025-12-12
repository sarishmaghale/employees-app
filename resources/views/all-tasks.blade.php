@extends('layout')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="page-title-section">
                <h2><i class="fas fa-tasks me-2" style="color: #3b82f6;">
                    </i>Event Lists</h2>
                <p class="page-subtitle">Review your task</p>
            </div>
            <button type="button" class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#newTaskModal">
                <i class="fas fa-plus me-2"></i>Add Event
            </button>
        </div>
    </div>

    <div class="table-wrapper">
        <div class="table-responsive">
            <h2>All Tasks</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody id="tasksTableBody">

                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadAllTasks();

            function loadAllTasks() {
                $('#tasksTableBody')
                $.ajax({
                    url: `{{ route('calendar.lists') }}`,
                    method: "GET",
                    dataType: "json",
                    success: function(tasks) {
                        let rows = "";
                        tasks.forEach(result => {
                            let tr = $("<tr>");
                            tr.append($("<td>").text(result.id));
                            tr.append($("<td>").text(result.title));
                            tr.append($("<td>").text(result.priority));
                            tr.append($("<td>").text(result.start));
                            tr.append($("<td>").text(result.end));
                            tr.append($("<td>").text(result.task_category ? result.task_category
                                .category_name : '-'));
                            $("#tasksTableBody").append(tr);
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", "Something went wrong", "error");
                        console.error('Error:' + xhr.responseText);
                    }
                });
            }
        })
    </script>
@endpush
