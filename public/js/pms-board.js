function initializePmsBoard(boardId)
{
    $(document).ready(function() {

            function renderTaskCard(task) {
                return (
                    '<div class="kb-card pms-task-item" data-task-id="' + task.id + '">' +
                    '<h3>' + task.title + '</h3>' +
                    '<div class="kb-card-meta">' +
                    '<span class="kb-tag"></span>' +
                    '<span class="kb-date">Due: ' + (task.end_date || 'N/A') + '</span>' +
                    '</div>' +
                    '</div>'
                );
            }

            function updateTaskCount(column) {
                const count = column.find('.kb-card').length;
                column.find('.task-count').text(`${count} Tasks`);
            }

            //show input for adding new task
            $(document).on('click', '.initiatePmsAddTaskBtn', function() {
                const btn = $(this);
                const cardId = btn.data('card-id');

                btn.hide();
                const form = $('#inline-task-template .inline-task-form').clone();
                form.find('.btn-save-task').data('card-id', cardId);
                btn.before(form);
                form.find('.inline-task-input').focus();
            });

            $(document).on('click', '.btn-cancel-task', function() {
                const form = $(this).closest('.inline-task-form');
                form.prev('.initiatePmsAddTaskBtn').show();
                form.remove();
            });

            //save new task to db
            $(document).on('click', '.btn-save-task', function() {
                const btn = $(this);
                const cardId = btn.data('card-id');
                const form = btn.closest('.inline-task-form');
                const input = form.find('.inline-task-input');
                const title = input.val().trim();
                const column = form.closest('.kb-column');
                const cardBody = column.find('.kb-column-body');

                if (!title) {
                    input.focus();
                    return;
                }

                $.ajax({
                    url: `/pms-add-task`,
                    method: "POST",
                    data: {
                        _token: document.querySelector('meta[name="csrf-token"]').content,
                        title: title,
                        card_id: cardId
                    },
                    success: function(response) {
                        if (response.success) {
                            const addBtn = cardBody.find('.initiatePmsAddTaskBtn');
                            addBtn.before(renderTaskCard(response.data));
                            updateTaskCount(column);
                            addBtn.show();
                            form.remove();
                        } else {
                            Swal.fire('Error', response.message, 'error');

                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) handleValidationErrors(xhr, form);
                        else {
                            Swal.fire('Error', 'Something went wrong', 'error');
                            console.error('Error:' + xhr.responseText);
                        }
                    }
                });
            });

            //Drag and drop task from one card to other
            function enableTaskDragOver() {

                $('.kb-column-body').each(function() {
                    new Sortable(this, {
                        group: 'cardTasks',
                        animation: 150,
                        handle: '.kb-card',
                        filter: '.kb-add-task-btn',
                        onEnd: function(evt) {
                            const taskId = $(evt.item).data('task-id');
                            const newCardId = $(evt.to).data('card-id');
                            const newPosition = $(evt.to).children('.kb-card').index(evt.item) +
                                1;
                            console.log('function wotking');

                            $.ajax({
                                url: `/pms-task-reorder`,
                                type: "POST",
                                data: {
                                    _token: document.querySelector('meta[name="csrf-token"]').content,
                                    task_id: taskId,
                                    new_card_id: newCardId,
                                    position: newPosition
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire('Success', response.message,
                                            'success');
                                    } else {
                                        Swal.fire('Error', response.message,
                                            'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', 'Something went wrong',
                                        'error');
                                    console.error(xhr.responseText);
                                }
                            });
                        }
                    })
                })
            }

            //open modal to fill details for adding new card
            $(document).on('click', '#pmsAddCardBtn', function() {
                $("#pmsAddNewCardModal").modal('show');
                $('#pmsAddNewCardModal').find('#pms_card_board_id').val(boardId);
            });

            //open modal to edit task
            $(document).on('click', '.pms-task-item', function() {
                const taskId = $(this).data('task-id');
                const modal = $('#pmsEditTaskModal');

                modal.modal('show');

                // Load task details- triggering function inside edit modal
                loadTaskDetails(taskId, modal);
            });

            //add new member to the opened board
            $(document).on('click', '.add-board-member', function(e) {
                e.preventDefault();
                let employeeId = $(this).data('id');
                $.ajax({
                    url: `/pms-board/${boardId}/add-member`,
                    type: "POST",
                    data: {
                        employee_id: employeeId,
                         _token: document.querySelector('meta[name="csrf-token"]').content,
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message, 'warning');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Something went wrong', 'error');
                        console.error('Error:', xhr.responseText);
                    }
                })
            })

            //acticate drag and drop for tasks
            enableTaskDragOver();
        });
}