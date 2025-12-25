<style>
    /* Modal body task list */
    #board-taskList {
        list-style: none;
        /* Remove default bullets */
        margin: 0;
        padding: 0;
    }

    /* Individual task items */
    .task-item {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        margin-bottom: 5px;
        background-color: #f8f9fa;
        /* light gray */
        border: 1px solid #dee2e6;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.2s, transform 0.1s;
    }

    /* Hover effect */
    .task-item:hover {
        background-color: #e2e6ea;
        transform: translateX(2px);
    }

    /* Active/selected task (optional) */
    .task-item.active {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
    }
</style>

<div class="modal fade" id="addTaskToBoardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul id="board-taskList" class="list-group">
                    <!-- Tasks will be populated here -->
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).on('click', '#board-taskList .task-item', function() {
            const taskId = $(this).data('task-id');
            const statusId = $('#addTaskToBoardModal').data('status-id');

            $.ajax({
                url: `{{ route('board.tasks.save') }}`,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    task_id: taskId,
                    status_id: statusId
                },
                success: function(response) {
                    if (response.success) {
                        $('#addTaskToBoardModal').modal('hide');
                        Swal.fire('Success', response.message, 'success');
                        document.dispatchEvent(new Event('board.refresh'));
                    } else Swal.fire('Error', response.message, 'error');
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Something went wrong', 'error');
                    console.error(xhr.responseText);
                }
            })
        })
    </script>
@endpush
