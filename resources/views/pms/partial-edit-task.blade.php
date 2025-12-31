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


            <div class="modal-body">
                <div class="row">

                    <!-- Left Column -->
                    <div class="col-lg-6">
                        <form id="pmsEditTaskForm">
                            <input type="hidden" name="id" id="pms_edit_task_id">
                            <div class="left-scrollable"
                                style="max-height: 70vh; overflow-y: auto; padding-right: 0.5rem;">

                                <!-- Action Buttons -->
                                <div class="mb-3 d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-link btn-sm" id="pmsAddChecklistBtn"><i
                                            class="fas fa-plus"></i> Add
                                        CheckList</button>

                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="pmsEditTaskDesc" class="form-label">Description</label>
                                    <textarea class="form-control" id="pmsEditTaskDesc" rows="4" name="description"
                                        placeholder="Add a more detailed description..."></textarea>
                                </div>

                                <!-- Dates in same line -->
                                <div class="mb-3 row g-2">
                                    <div class="col-md-6">
                                        <label for="pmsEditTaskStart" class="form-label">Start Date</label>
                                        <input type="text" class="form-control datepicker" name="start_date"
                                            id="pmsEditTaskStart">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pmsEditTaskEnd" class="form-label">End Date</label>
                                        <input type="date" class="form-control datepicker" name="end_date"
                                            id="pmsEditTaskEnd">
                                    </div>
                                </div>

                                <!-- Checklist -->
                                <div class="mb-3" id="taskChecklistContainer"></div>

                                <!-- Save & Cancel Buttons -->
                                <div class="d-flex gap-2 mb-3">
                                    <button type="submit" class="btn btn-primary" id="pmsUpdateTaskBtn">Save</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>

                            </div>
                        </form>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-6">
                        <h6>Comments & Activity</h6>
                        <form id="pmsTaskCommentForm">
                            <div class="d-flex align-items-end mb-3 comment-box">
                                <textarea class="form-control flex-grow-1" name="comment" id="commentInput" rows="3"
                                    placeholder="Write a comment..." required></textarea>

                                <button id="postCommentBtn" class="btn btn-primary ms-2 btn-circle"
                                    title="Post Comment">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Comments container -->
                        <div id="commentsContainer"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Checklist Modal -->
<div class="modal fade" id="pmsAddChecklistModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3 bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Add Checklist</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="pmsAddchecklistForm">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="checklistTitleInput"
                            placeholder="Checklist Title" required>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-sm">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/pms-task.js') }}"></script>
    <script>
        function loadTaskDetails(taskId, modal) {
            modal.find('#pms_edit_task_id').val(taskId);
            console.log(taskId);

            $.ajax({
                url: `/pms-task-detail/${taskId}`,
                method: "GET",
                success: function(response) {
                    if (!response.success) {
                        return Swal.fire("Error", response.message, "error");
                    }

                    const task = response.data;
                    modal.find('#pmsEditTaskTitle').html(`<i class="far fa-circle"></i> ${task.title}`);
                    modal.find('#pmsEditTaskDesc').val(task.description ?? "");
                    modal.find('#pmsEditTaskStart').val(task.start_date ?? "");
                    modal.find('#pmsEditTaskEnd').val(task.end_date ?? "");

                    renderComments(task.comments ?? [], modal.find('#commentsContainer'));
                    renderCheckList(task.checklists ?? []);
                },
                error: function(xhr) {
                    Swal.fire("Error", "Failed to load task details", "error");
                    console.error(xhr.responseText);
                }
            });
        };

        $(document).on('input', '#commentInput', function() {
            $(this).removeClass('is-invalid');
        });

        $(document).on('click', '#pmsAddChecklistBtn', () => {
            $('#pmsAddChecklistModal').modal('show');
        });
    </script>
@endpush
