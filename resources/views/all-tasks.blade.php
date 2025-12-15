@extends('layout')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div style="margin-bottom: 30px;">
            <h2 style="font-size: 28px; font-weight: 600; color: #1f2937; margin: 0;">
                <i class="fas fa-tasks me-2" style="color: #3b82f6;">
                </i>Event Lists
            </h2>
        </div>

        <!-- Filter Section -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; gap: 20px;">
            <!-- Left Side - Date Filters, Category and Search Button -->
            <div style="display: flex; gap: 15px; align-items: flex-end; flex: 1;">
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 14px; font-weight: 500; color: #6b7280;">Start Date</label>
                    <input type="text" id="startDate" class="datepicker"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 180px; outline: none;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 14px; font-weight: 500; color: #6b7280;">End Date</label>
                    <input type="text" id="endDate" class="datepicker"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 180px; outline: none;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 14px; font-weight: 500; color: #6b7280;">Category Type</label>
                    <select id="categorySelect"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 180px; outline: none; background: white; cursor: pointer;">
                        <option value="" selected>All categories</option>
                        @forelse(getTaskCategories() as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @empty
                            <option value="">No categories found</option>
                        @endforelse
                    </select>
                </div>

                <button type="button" id="searchButton"
                    style="background: #3b82f6; border: none; color: white; padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s; height: 40px;"
                    onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                    <i class="fas fa-search" style="margin-right: 8px;"></i>Search
                </button>
                <button type="button" id="resetButton"
                    style="background: #3b82f6; border: none; color: white; padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s; height: 40px;"
                    onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                    <i class="fas fa-undo" style="margin-right: 8px;"></i>Reset
                </button>
            </div>

            <!-- Right Side - Buttons -->
            <div style="display: flex; gap: 12px;">
                <button type="button" class="btn btn-primary"
                    onclick="window.location.href='{{ route('calendar.index') }}'"
                    style="background: #ef4444; border: none; color: white; padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s;"
                    onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                    Calendar
                </button>

                <button type="button" class="btn btn-primary" id="newTaskModal"
                    style="background: #3b82f6; border: none; color: white; padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s;"
                    onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                    <i class="fas fa-plus" style="margin-right: 8px;"></i>Add Event
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Event Type</th>
                            <th>Event Title</th>
                            @if (Auth::user()->role === 'admin')
                                <th>Employee</th>
                            @endif
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Badge</th>
                        </tr>
                    </thead>
                    <tbody id="tasksTableBody">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('partial-views.add-event-partial')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            loadAllTasks();
            $('#searchButton').click(function() {
                let filters = {
                    start: $('#startDate').val(),
                    end: $('#endDate').val(),
                    category_id: $('#categorySelect').val()
                };
                loadAllTasks(filters);
            });
            $('#resetButton').click(function() {
                $('#startDate').val('');
                $('#endDate').val('');
                $('#categorySelect').val('');
                loadAllTasks();
            });
            let userRole = "{{ auth()->user()->role }}";

            function loadAllTasks(filters = {}) {
                $('#tasksTableBody').empty();
                $.ajax({
                    url: `{{ route('tasks.lists') }}`,
                    method: "GET",
                    data: filters,
                    dataType: "json",
                    success: function(tasks) {
                        let rows = "";
                        tasks.forEach(result => {
                            let tr = $("<tr>");
                            tr.append($("<td>").text(result.task_category ? result.task_category
                                .category_name : '-'));
                            tr.append($("<td>").text(result.title));
                            if (userRole === 'admin') {
                                tr.append($("<td>").text(result.employee.username));
                            }
                            tr.append($("<td>").text(result.start));
                            tr.append($("<td>").text(result.end));
                            tr.append($("<td>").text());
                            $("#tasksTableBody").append(tr);
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", "Something went wrong", "error");
                        console.error('Error:' + xhr.responseText);
                    }
                });
            }

            $(document).on('click', '#newTaskModal', function() {
                $('#addTaskModal').modal('show');
            })
        })
    </script>
@endpush
