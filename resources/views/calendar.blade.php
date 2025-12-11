@extends('layout')

@push('styles')
    <style>
        /* Custom radio button colors */
        #badge_work:checked {
            background-color: #007bff;
            border-color: #007bff;
        }

        #badge_travel:checked {
            background-color: #ffa500;
            border-color: #ffa500;
        }

        #badge_appointment:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        #badge_important:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        /* Focus states */
        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            outline: none;
        }

        /* Button hover effects */
        .modal-footer .btn:hover {
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .modal-footer .btn-primary:hover {
            background: #0056b3;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
        }
    </style>
@endpush

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
                                    Type:</label>
                                <select class="form-select" name="event_type"
                                    style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #333;">
                                    <option>Personal</option>
                                    <option>Work</option>
                                    <option>Meeting</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">Category:</label>
                                <select class="form-select" name="category"
                                    style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #333;">
                                    <option>Personal</option>
                                    <option>Project</option>
                                    <option>Organization</option>
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
                                <input type="date" class="form-control" name="start_date" placeholder="Start Date"
                                    style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #999;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 8px;">To:</label>
                                <input type="date" class="form-control" name="end_date" placeholder="End Date"
                                    style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #999;">
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

                        <!-- Badge Priority -->
                        <div class="mb-3">
                            <label class="form-label"
                                style="font-size: 13px; color: #999; font-weight: 500; margin-bottom: 12px;">Badge:</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="isImportant" id="badge_work"
                                        value="0" checked
                                        style="width: 18px; height: 18px; border: 2px solid #007bff; cursor: pointer;">
                                    <label class="form-check-label" for="badge_work"
                                        style="font-size: 14px; color: #666; margin-left: 6px; cursor: pointer;">Work</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="isImportant" id="badge_travel"
                                        value="0"
                                        style="width: 18px; height: 18px; border: 2px solid #ffa500; cursor: pointer;">
                                    <label class="form-check-label" for="badge_travel"
                                        style="font-size: 14px; color: #666; margin-left: 6px; cursor: pointer;">Travel</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="isImportant" id="badge_appointment"
                                        value="0"
                                        style="width: 18px; height: 18px; border: 2px solid #28a745; cursor: pointer;">
                                    <label class="form-check-label" for="badge_appointment"
                                        style="font-size: 14px; color: #666; margin-left: 6px; cursor: pointer;">Appointment</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="isImportant"
                                        id="badge_important" value="1"
                                        style="width: 18px; height: 18px; border: 2px solid #dc3545; cursor: pointer;">
                                    <label class="form-check-label" for="badge_important"
                                        style="font-size: 14px; color: #666; margin-left: 6px; cursor: pointer;">Important</label>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="modal-footer"
                    style="border-top: none; padding: 0 24px 24px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button class="btn" data-bs-dismiss="modal"
                        style="background: white; color: #007bff; border: 1px solid #e0e0e0; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px;">Discard</button>
                    <button class="btn btn-primary" id="saveTaskBtn"
                        style="background: #007bff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">Add
                        Task</button>
                </div>

            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
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
                let currentStart = null;
                let currentEnd = null;

                calendar.on('dateClick', function(info) {
                    isEditing = false;
                    editEventId = null;
                    $('#taskForm').trigger('reset');
                    currentStart = info.dateStr;
                    currentEnd = info.dateStr;
                    $('#TaskModal').modal('show');
                });

                calendar.on('eventClick', function(info) {
                    isEditing = true;
                    editEventId = info.event.id;
                    $('#taskForm').trigger('reset');
                    $('#task_title').val(info.event.title);
                    $('#task_priority').val(info.event.extendedProps.isImportant || 0);
                    currentStart = info.event.startStr;
                    currentEnd = info.event.startStr;
                    $('#TaskModal').modal('show');
                });

                $('#saveTaskBtn').on('click', function(e) {
                    e.preventDefault();
                    let title = $('#task_title').val().trim();
                    if (title === "" && isEditing) {
                        console.log(editEventId);
                        $.ajax({
                            url: `/task/${editEventId}`,
                            method: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    $('#TaskModal').modal('hide');
                                    calendar.refetchEvents();
                                }
                            },
                            error: function(xhr) {
                                console.error('Error:', xhr.responseText);
                            }
                        });
                        return;
                    } else {
                        let formData = new FormData($('#taskForm')[0]);
                        formData.append('start', currentStart);
                        formData.append('end', currentEnd);
                        formData.append('_token', '{{ csrf_token() }}');
                        let url = isEditing ? `/task/update/${editEventId}` : `{{ route('task.store') }}`;
                        $.ajax({
                            url: url,
                            method: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Success', response.message, 'success');
                                    $('#TaskModal').modal('hide');
                                    calendar.refetchEvents();
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) handleValidationErrors(xhr, '#taskForm');
                                else console.error('Error:', xhr.responseText);
                            }
                        });
                    }

                })
                calendar.on('eventDrop', function(info) {
                    let request = new FormData();
                    request.append('title', info.event.title);
                    request.append('isImportant', info.event.extendedProps.isImportant);
                    request.append('start', info.event.startStr);
                    request.append('end', info.event.startStr);
                    updateTask(info.event.id, request);
                });

                function updateTask(id, formData) {
                    formData.append('_token', '{{ csrf_token() }}');
                    $.ajax({
                        url: `/task/update/${id}`,
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Updated', response.message, 'success');
                                calendar.refetchEvents();
                            } else alert(response.message);
                        },
                        error: function(xhr) {
                            console.error('Error:' + xhr.responseText);
                        }
                    });
                }
                calendar.render()
            });
        </script>
    @endpush
