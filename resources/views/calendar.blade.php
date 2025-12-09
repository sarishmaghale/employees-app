@extends('layout')

@section('content')
    <div id="calendar-container">
        <div id="calendar"></div>
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

            calendar.on('dateClick', function(info) {
                console.log('clicled on:' + info.dateStr);
                let title = prompt("Task Title:");
                if (!title) return;
                let request = new FormData();
                request.append('title', title);
                request.append('start', info.dateStr);
                request.append('end', info.dateStr);
                request.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: `{{ route('calendar.store') }}`,
                    method: "POST",
                    data: request,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');
                            calendar.refetchEvents();
                        } else alert(response.message);
                    },
                    error: function(xhr) {
                        console.error("error:", xhr.responseText);
                    }
                });
            });

            calendar.on('eventClick', function(info) {
                let newTitle = prompt("Edit Task: ", info.event.title);
                if (newTitle === null) return;
                if (newTitle === "") {
                    $.ajax({
                        url: `/task/${info.event.id}`,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.message, 'success');
                                info.event.remove();
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr.responseText);
                        }
                    })

                } else {
                    let request = new FormData();
                    request.append('title', newTitle);
                    request.append('start', info.event.startStr);
                    request.append('end', info.event.startStr);
                    updateTask(info.event.id, request);
                }

            });

            calendar.on('eventDrop', function(info) {
                let request = new FormData();
                request.append('title', info.event.title);
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
