@extends('layout')

@section('content')
    <div id="calendar-container">
        <div id="calendar"></div>
    </div>
    <!-- Add Task Modal -->
    <div class="modal fade" id="TaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="taskForm">
                        <div class="mb-3">
                            <label>Task Title</label>
                            <input type="text" class="form-control" name="title" id="task_title">
                        </div>
                        <div class="mb-3">
                            <label>Priority</label>
                            <select name="isImportant" id="task_priority" class="form-control">
                                <option value="0">Normal</option>
                                <option value="1">Important</option>
                            </select>
                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button class="btn btn-primary" id="saveTaskBtn">Save Task</button>
                </div>

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
                            console.error('Error:', xhr.responseText);
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
