@extends('layout')

@section('content')
    <!-- Calendar Header with Filters -->
    <div style="background: white; padding: 20px 30px; margin-bottom: 0; border-bottom: 1px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <!-- Left Side - Filter Buttons -->
            <div style="display: flex; gap: 12px; align-items: center;">
                <button class="category-filter active" data-category-id=""
                    style="padding: 8px 16px; border: none; background: #1f2937; color: white; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                    <span
                        style="display: inline-block; width: 10px; height: 10px; background: #1f2937; border-radius: 2px; margin-right: 8px;"></span>All
                </button>

                @forelse(getTaskCategories() as $category)
                    <button class="category-filter" data-category-id="{{ $category->id }}" data-color="{{ $category->color }}"
                        style="padding:8px 16px; border:1px solid #d1d5db; background:white; color:#4b5563; border-radius:6px; font-size:14px; font-weight:500; cursor:pointer;">
                        <span
                            style="display:inline-block;width:10px;height:10px; background: {{ $category->color }};border-radius:2px; margin-right:8px;">
                        </span>
                        {{ $category->category_name }}
                    @empty
                @endforelse
            </div>

            <!-- Right Side - Action Buttons -->
            <div style="display: flex; gap: 12px;">
                <button type="button"
                    style="background: #3b82f6; border: none; color: white; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; gap: 8px;"
                    id="openAddEventModal" onmouseover="this.style.background='#2563eb'"
                    onmouseout="this.style.background='#3b82f6'">
                    <i class="fas fa-calendar-plus"></i>Add Event
                </button>

                <a class="btn"
                    style="background: #f59e0b; border: none; color: white; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; gap: 8px;"
                    href="{{ route('tasks.all') }}" onmouseover="this.style.background='#d97706'"
                    onmouseout="this.style.background='#f59e0b'">
                    <i class="fas fa-list"></i>Event Lists
                </a>
            </div>
        </div>
    </div>

    <div id="calendar-container">
        <div id="calendar"></div>
    </div>
    <!-- Add Task Modal -->
    @include('partial-views.add-event-partial')
    @include('partial-views.edit-event-partial')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let activeCategoryId = null;
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) {
                console.error("Calendar element not found");
            }
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                editable: true,
                selectable: true,
                eventDurationEditable: true,
                eventStartEditable: true,
                eventResizableFromStart: true,
                eventSources: [{
                    url: `{{ route('calendar.show') }}`,
                    method: "GET",
                    extraParams: function() {
                        return activeCategoryId ? {
                            category_id: activeCategoryId
                        } : {};
                    },
                    failure: () => alert('Failure to load tasks'),
                }]
            });


            calendar.on('dateClick', function(info) {
                $('#add_task_start').val(info.dateStr);
                $('#add_task_end').val(info.dateStr);
                $('#employee_id').val(`{{ auth()->user()->id }}`);
                $('#addTaskModal').modal('show');
            });

            calendar.on('eventClick', function(info) {

                const categoryId = info.event.extendedProps.category_id;
                const badgeValue = info.event.extendedProps.badge;
                const employeeId = info.event.extendedProps.employee_id;
                console.log(info.event.extendedProps.employee.id)
                $('#edit_task_id').val(info.event.id);
                $('#edit_task_title').val(info.event.title);
                $('#edit_task_category').val(categoryId || 0);
                $('#edit_task_start').val(info.event.startStr);
                $('#edit_task_end').val(info.event.endStr ? info.event.endStr :
                    info.event.startStr);
                if (badgeValue) {
                    $('.subCategoryGroup').hide();
                    $(`.subCategoryGroup[data-category="${categoryId}"]`).show();
                    $('input[name="badge"][value="' + badgeValue + '"]').prop('checked', true);
                    $(`.subCategoryGroup[data-category="${categoryId}"] input[name="badge"][value="${badgeValue}"]`)
                        .prop('checked', true);
                }

                $('#edit_task_employee').val(employeeId);

                $('#editTaskModal').modal('show');
            });

            calendar.on('eventDrop', updateEvent);
            calendar.on('eventResize', updateEvent);

            calendar.render();

            document.addEventListener('calendar:refresh', function() {
                calendar.refetchEvents();
            });

            function updateEvent(info) {
                const request = new FormData();
                request.append('title', info.event.title);
                request.append('category_id', info.event.extendedProps.category_id);
                request.append('start', info.event.startStr);
                request.append('end', info.event.endStr ? info.event.endStr : info.event
                    .startStr);
                request.append('badge', info.event.extendedProps.badge);
                request.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: `/task/update/${info.event.id}`,
                    method: 'POST',
                    data: request,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');
                            calendar.refetchEvents();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            $(document).on('click', '#openAddEventModal', function() {
                $('#addTaskModal').modal('show');
            });

            $('.category-filter').on('click', function() {
                activeCategoryId = $(this).data('category-id') ?? null
                calendar.refetchEvents();
            })

        });
    </script>
@endpush
