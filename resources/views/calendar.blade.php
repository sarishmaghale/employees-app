@extends('layout')

@section('content')
    <div id="calendar-container">
        <div id="calendar"></div>
    </div>
    <!-- Add Task Modal -->
    <div class="modal fade" id="TaskModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">

                <div class="modal-header" style="border-bottom: 1px solid #e8e8e8; padding: 20px 24px;">
                    <h5 class="modal-title" style="font-weight: 600; color: #1a1a1a; font-size: 18px;">Add Events</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 12px;"></button>
                </div>

                <div class="modal-body" style="padding: 24px;">
                    <form id="taskForm">

                        <!-- Event Type and Category -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label"
                                    style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Event
                                    Category:</label>
                                <select class="form-select" name="category_id" id="task_category"
                                    style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #333;">
                                    @forelse(getTaskCategories() as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @empty
                                        <option value="">No categories found</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Priority:</label>
                                <select class="form-select" name="isImportant" id="task_priority"
                                    style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #333;">
                                    <option value="0">Normal</option>
                                    <option value="2">Moderate</option>
                                    <option value="1">Important</option>
                                </select>
                            </div>
                        </div>

                        <!-- Event Title -->
                        <div class="mb-4">
                            <label class="form-label"
                                style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Event
                                Title:</label>
                            <input type="text" class="form-control" name="title" id="task_title"
                                placeholder="Enter Title"
                                style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;">
                        </div>

                        <!-- From and To Dates -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label"
                                    style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">From:</label>
                                <input type="date" class="form-control" id="task_start_date" name="start"
                                    placeholder="Start Date"
                                    style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; ">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">To:</label>
                                <input type="date" class="form-control" id="task_end_date" name="end"
                                    placeholder="End Date"
                                    style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px;">
                            </div>
                        </div>

                        <!-- Event Description -->
                        <div class="mb-4">
                            <label class="form-label"
                                style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Event
                                Description:</label>
                            <textarea class="form-control" rows="4" placeholder="Enter Description"
                                style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; resize: none;"></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer"
                    style="border-top: none; padding: 0 24px 24px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button class="btn" data-bs-dismiss="modal"
                        style="background: white; color: #007bff; border: 1px solid #e0e0e0; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px;">Discard</button>
                    <button class="btn btn-danger" id="deleteTaskBtn"
                        style="display: none; background: #dc3545; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px; box-shadow: 0 2px 8px rgba(220,53,69,0.3);">
                        Delete
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveTaskBtn"
                        style="background: #007bff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">Add
                        Task</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        import {
            saveNewTask,
            updateExistingTask,
            deleteTask
        } from "{{ asset('js/task.js') }}";

        $(document).ready(function() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) {
                console.error("Calendar element not found");
            }
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                editable: true,
                selectable: true,
                eventSources: [{
                    url: `{{ route('calendar.lists') }}`,
                    method: "GET",
                    failure: () => alert('Failure to load tasks'),
                }]
            });

            let isEditing = false;
            let editEventId = null;

            calendar.on('dateClick', function(info) {
                isEditing = false;
                editEventId = null;

                $('#task_start_date').val(info.dateStr);
                $('#task_end_date').val(info.dateStr);
                $('#deleteTaskBtn').hide();
                $('#TaskModal').modal('show');
            });

            calendar.on('eventClick', function(info) {
                isEditing = true;
                editEventId = info.event.id;
                $('#task_title').val(info.event.title);
                $('#task_category').val(info.event.extendedProps.category_id || 0);
                $('#task_priority').val(info.event.extendedProps.isImportant || 0);
                $('#task_start_date').val(info.event.startStr);
                $('#task_end_date').val(info.event.endStr ? info.event.endStr : info.event.startStr);
                $('#deleteTaskBtn').show();
                $('#TaskModal').modal('show');
            });

            $('#saveTaskBtn').on('click', function(e) {
                e.preventDefault();
                let title = $('#task_title').val().trim();
                const formData = new FormData($('#taskForm')[0]);
                formData.append('_token', '{{ csrf_token() }}');
                isEditing ? updateExistingTask(formData, calendar, '#TaskModal', editEventId) :
                    saveNewTask(formData, calendar, '#TaskModal');
            })

            $('#deleteTaskBtn').on('click', function(e) {
                e.preventDefault();
                if (!editEventId) return;
                const csrf = '{{ csrf_token() }}';
                deleteTask(csrf, editEventId, calendar, '#TaskModal')

            })
            calendar.on('eventDrop', function(info) {
                const request = new FormData();
                request.append('title', info.event.title);
                request.append('category_id', info.event.extendedProps.category_id);
                request.append('isImportant', info.event.extendedProps.isImportant);
                request.append('start', info.event.startStr);
                request.append('end', info.event.endStr ? info.event.endStr : info.event.startStr);
                request.append('_token', '{{ csrf_token() }}');
                updateExistingTask(request, calendar, '#TaskModal', info.event.id)
            });

            calendar.render()

            $('#TaskModal').on('hidden.bs.modal', function() {
                $('#taskForm')[0].reset();
                $('#taskForm .is-invalid').removeClass('is-invalid');
                $('#taskForm .invalid-feedback').remove();
            });

        });
    </script>
@endpush
