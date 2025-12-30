<!-- Minimal Bootstrap Modal -->
<div class="modal fade" id="pmsEditTaskModal" tabindex="-1" aria-labelledby="cardDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="pmsEditTaskTitle">
                    <i class="far fa-circle"></i> Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="pmsEditTaskForm">
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-lg-6">
                            <div class="left-scrollable"
                                style="max-height: 70vh; overflow-y: auto; padding-right: 0.5rem;">

                                <!-- Action Buttons -->
                                <div class="mb-3 d-flex gap-2 flex-wrap">
                                    <button class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus"></i> Add
                                        Checkbox</button>
                                    <button class="btn btn-outline-secondary btn-sm"><i class="fas fa-user"></i>
                                        Members</button>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="pmsEditTaskDesc" class="form-label">Description</label>
                                    <textarea class="form-control" id="pmsEditTaskDesc" rows="4" placeholder="Add a more detailed description..."></textarea>
                                </div>

                                <!-- Dates in same line -->
                                <div class="mb-3 row g-2">
                                    <div class="col-md-6">
                                        <label for="pmsEditTaskStart" class="form-label">Start Date</label>
                                        <input type="text" class="form-control datepicker" id="pmsEditTaskStart">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pmsEditTaskEnd" class="form-label">End Date</label>
                                        <input type="date" class="form-control datepicker" id="pmsEditTaskEnd">
                                    </div>
                                </div>

                                <!-- Checklist -->
                                <div class="mb-3">
                                    <h6>Create DB</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkItem1" checked>
                                        <label class="form-check-label" for="checkItem1">One</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkItem2">
                                        <label class="form-check-label" for="checkItem2">Two</label>
                                    </div>
                                </div>

                                <!-- Save & Cancel Buttons -->
                                <div class="d-flex gap-2 mb-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>

                            </div>
                        </div>


                        <!-- Right Column -->
                        <div class="col-lg-6">
                            <h6>Comments & Activity</h6>
                            <textarea class="form-control mb-3" id="commentInput" rows="2" placeholder="Write a comment..."></textarea>

                            <!-- Comments container -->
                            <div id="commentsContainer"></div>
                        </div>

                    </div>
                </div>
        </div>
        </form>

    </div>
</div>
</div>


@push('scripts')
    <script>
        function loadTaskDetails(taskId, modal) {
            console.log(taskId);
            // GET Task Details
            $.ajax({
                url: `/pms-task-detail/${taskId}`,
                method: "GET",
                success: function(response) {
                    if (response.success) {
                        const task = response.data;
                        modal.find('#pmsEditTaskTitle').html(
                            `<i class="far fa-circle"></i> ${task.title}`
                        );
                        modal.find('#pmsEditTaskDesc').val(task.description ?? "");
                        modal.find('#pmsEditTaskStart').val(task.start_date ?? "");
                        modal.find('#pmsEditTaskEnd').val(task.end_date ?? "");

                        const activityContainer = modal.find('#commentsContainer');
                        renderComments(task.comments ?? [], activityContainer);
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error", "Failed to load task details", "error");
                    console.error(xhr.responseText);
                }
            });
        };

        function renderComments(comments, container) {
            container.empty(); // clear previous comments

            if (comments.length > 0) {
                comments.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                comments.forEach(comment => {
                    const createdAt = new Date(comment.created_at);
                    const formattedDate = createdAt.toLocaleString('en-GB', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });
                    const commentHtml = `
                <div class="d-flex align-items-start mb-2">
                    <div class="rounded-circle bg-warning text-center me-2"
                         style="width:32px; height:32px; line-height:32px;">
                        ${comment.employee.username.charAt(0) || 'U'}
                    </div>
                    <div>
                        <strong>${comment.employee.username}</strong> ${comment.comment}<br>
                        <small class="text-muted">${formattedDate}</small>
                    </div>
                </div>
            `;
                    container.append(commentHtml);
                });
            } else {
                container.append('<div class="text-muted">No comments yet.</div>');
            }
        }

        $('#pmsEditTaskModal').on('hidden.bs.modal', function() {
            $(this).find('#pmsEditTaskForm')[0].reset();
            $('#pmsEditTaskTitle').html('<i class="far fa-circle"></i> Details');
            $('.card-details-checklist-items').empty();
            $('.card-details-activity').empty();
        });
    </script>
@endpush
