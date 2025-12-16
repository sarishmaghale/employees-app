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
        <div
            style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; gap: 20px; flex-wrap: wrap;">
            <!-- Left Side - Date Filters, Category and Search Button -->
            <div style="display: flex; gap: 15px; align-items: flex-end; flex: 1; flex-wrap: wrap; min-width: 300px;">
                <div style="display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 150px;">
                    <label style="font-size: 14px; font-weight: 500; color: #6b7280;">Start Date</label>
                    <input type="text" id="startDate" class="datepicker"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 100%; outline: none;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 150px;">
                    <label style="font-size: 14px; font-weight: 500; color: #6b7280;">End Date</label>
                    <input type="text" id="endDate" class="datepicker"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 100%; outline: none;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 150px;">
                    <label style="font-size: 14px; font-weight: 500; color: #6b7280;">Category Type</label>
                    <select id="categorySelect"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 100%; outline: none; background: white; cursor: pointer;">
                        <option value="" selected>All categories</option>
                        @forelse(getTaskCategories() as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @empty
                            <option value="">No categories found</option>
                        @endforelse
                    </select>
                </div>

                @if (Auth::user()->role === 'admin')
                    <div style="display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 150px;">
                        <label style="font-size: 14px; font-weight: 500; color: #6b7280;">Emlployee</label>
                        <select id="employeeSelect"
                            style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 100%; outline: none; background: white; cursor: pointer;">
                            <option value="" selected>All Employees</option>
                            @forelse(getEmployees() as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->username }}</option>
                            @empty
                                <option value="">No employees found</option>
                            @endforelse
                        </select>
                    </div>
                @endif

                <button type="button" id="searchButton"
                    style="background: #3b82f6; border: none; color: white; padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s; height: 40px; align-self: flex-end; white-space: nowrap;"
                    onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                    <i class="fas fa-search" style="margin-right: 8px;"></i>Search
                </button>
                <button type="button" id="resetButton"
                    style="background: #3b82f6; border: none; color: white; padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s; height: 40px; align-self: flex-end; white-space: nowrap;"
                    onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                    <i class="fas fa-undo" style="margin-right: 8px;"></i>Reset
                </button>
            </div>

            <!-- Right Side - Buttons -->
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <button type="button" class="btn btn-primary"
                    onclick="window.location.href='{{ route('calendar.index') }}'"
                    style="background: #ef4444; border: none; color: white; padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s; white-space: nowrap;"
                    onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                    Calendar
                </button>

                <button type="button" class="btn btn-primary" id="newTaskModal"
                    style="background: #3b82f6; border: none; color: white; padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s; white-space: nowrap;"
                    onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                    <i class="fas fa-plus" style="margin-right: 8px;"></i>Add Event
                </button>
            </div>
        </div>

        @include('partial-views.all-tasks-partial')
    </div>
    @include('partial-views.add-event-partial')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            loadAllTasks();
            const emp = $('#employeeSelect').val();
            console.log(emp)
            $('#searchButton').click(function() {
                let filters = {
                    start: $('#startDate').val(),
                    end: $('#endDate').val(),
                    category_id: $('#categorySelect').val(),
                    employee_id: $('#employeeSelect').val(),
                };
                loadAllTasks(filters);
            });
            $('#resetButton').click(function() {
                $('#startDate').val('');
                $('#endDate').val('');
                $('#categorySelect').val('');
                loadAllTasks();
            });

            $(document).on('click', '#newTaskModal', function() {
                $('#addTaskModal').modal('show');
            })
        })
    </script>
@endpush
