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

@push('scripts')
    <script>
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
                        tr.append($("<td>").text(result.badge));
                        $("#tasksTableBody").append(tr);
                    });
                },
                error: function(xhr) {
                    Swal.fire("Error!", "Something went wrong", "error");
                    console.error('Error:' + xhr.responseText);
                }
            });
        }
    </script>
@endpush
