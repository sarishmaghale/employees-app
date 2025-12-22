<!-- Task Details Modal -->
<div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-labelledby="taskDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailsModalLabel">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="taskDetailsContent">
                    <p><strong>Title:</strong> <span id="taskTitle"></span></p>
                    <p><strong>Event Type:</strong> <span id="taskType"></span></p>
                    <p><strong>Start:</strong> <span id="taskStart"></span></p>
                    <p><strong>End:</strong> <span id="taskEnd"></span></p>
                    <p><strong>Badge:</strong> <span id="taskBadge"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('calendar.index') }}" id="goToCalendarBtn" class="btn btn-primary">Go to Calendar</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
