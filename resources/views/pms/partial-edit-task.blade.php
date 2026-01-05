<!-- Minimal Bootstrap Modal -->
<div class="modal fade" id="pmsEditTaskModal" tabindex="-1" aria-labelledby="cardDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header d-flex align-items-center justify-content-between">

                <!-- Left: Title + Members -->
                <div class="d-flex align-items-center gap-3">

                    <h5 class="modal-title d-flex align-items-center gap-2 mb-0" id="pmsEditTaskTitle">
                        <i class="far fa-circle"></i> Details
                    </h5>

                    <!-- Stacked Member Avatars -->
                    <div class="d-flex align-items-center member-stack" id="assignedEmployeesContainer">

                    </div>
                </div>

                <!-- Right: Close -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">
                <div class="row">

                    <!-- Left Column -->
                    <div class="col-lg-6">
                        <form id="pmsEditTaskForm">
                            <input type="hidden" name="id" id="pms_edit_task_id">

                            <!-- Action Buttons -->
                            <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-1">

                                <!-- Left side actions -->
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button"
                                        class="btn btn-link btn-sm py-0 px-1 d-flex align-items-center"
                                        id="pmsAddChecklistBtn">
                                        <i class="fas fa-plus"></i> Add CheckList
                                    </button>

                                    <!-- Add Member -->
                                    <div class="dropdown">
                                        <button type="button"
                                            class="btn btn-link btn-sm py-0 px-1 d-flex align-items-center dropdown-toggle"
                                            id="addTaskMemberBtn" data-bs-toggle="dropdown">
                                            + Add Member
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-scroll">
                                            @foreach ($boardMembers as $employee)
                                                @php
                                                    $profile = $employee->detail?->profile_image ?? null;
                                                    $username = $employee->username ?? 'U';
                                                @endphp

                                                <li>
                                                    <a class="dropdown-item add-task-member d-flex align-items-center gap-2"
                                                        data-id="{{ $employee->id }}" href="#">
                                                        @if ($profile)
                                                            <img src="{{ asset('storage/' . $profile) }}"
                                                                class="rounded-circle"
                                                                style="width:32px;height:32px;object-fit:cover;">
                                                        @else
                                                            <div class="rounded-circle bg-warning text-white text-center"
                                                                style="width:32px;height:32px;line-height:32px;font-weight:600;">
                                                                {{ strtoupper(substr($username, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <span>{{ $username }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    {{-- Add Label --}}
                                    <div class="dropdown">
                                        <button type="button"
                                            class="btn btn-link btn-sm py-0 px-1 d-flex align-items-center dropdown-toggle"
                                            id="addTaskLabelBtn" data-bs-toggle="dropdown">
                                            + Add Label
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-scroll">
                                            @forelse (getLabels() as $label)
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input add-task-label-checkbox"
                                                        type="checkbox" value="{{ $label->id }}"
                                                        id="label{{ $label->id }}">
                                                    <label class="form-check-label" for="label{{ $label->id }}"
                                                        style="background-color: {{ $label->color }}; color:#fff; padding:2px 6px; border-radius:4px;">
                                                        {{ $label->title }}
                                                    </label>
                                                </div>
                                            @empty
                                            @endforelse
                                        </div>
                                    </div>

                                    {{-- Attach file --}}
                                    <div>
                                        <label for="pmsTaskAttachment"
                                            class="btn btn-link btn-sm py-0 px-1 d-flex align-items-center">
                                            <i class="fas fa-paperclip"></i> Attach File
                                        </label>
                                        <input type="file" id="pmsTaskAttachment" class="d-none" multiple>
                                    </div>
                                </div>

                            </div>

                            <div class="left-scrollable"
                                style="max-height: 50vh; overflow-y: auto; padding-right: 0.5rem;">
                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="pmsEditTaskDesc" class="form-label">Description</label>
                                    <textarea class="form-control" id="pmsEditTaskDesc" rows="4" name="description"
                                        placeholder="Add a more detailed description..."></textarea>
                                </div>
                                <!-- File Attachments -->
                                <div class="mb-3" id="selectedFilesContainer"
                                    style="font-size:0.9em; color:#555; display: none;">
                                    <!-- Files will be appended here dynamically -->
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
                                <div class="d-flex gap-2 mb-1">
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
                        <div id="commentsContainer" class="right-scrollable"
                            style="max-height: 40vh; overflow-y: auto; "></div>
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
        const modal = $('#pmsEditTaskModal');

        modal.on('shown.bs.modal', function() {
            const taskId = modal.data('task-id');
            console.log("Task ID in partial JS:", taskId);
            initializeTaskDetails(taskId, modal);
        });

        $(document).on('input', '#commentInput', function() {
            $(this).removeClass('is-invalid');
        });

        $(document).on('click', '#pmsAddChecklistBtn', () => {
            $('#pmsAddChecklistModal').modal('show');
        });
    </script>
@endpush
