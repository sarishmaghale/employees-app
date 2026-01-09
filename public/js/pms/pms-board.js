function initializePmsBoard(boardId)
{

            function renderTaskCard(task) {
                return (
                    '<div class="kb-card pms-task-item" data-task-id="' + task.id + '">' +
                    '<h3>' + task.title + '</h3>' +
                    '<div class="kb-card-meta">' +
                    '<span class="kb-tag"></span>' +
                    '</div>' +
                    '</div>'
                );
            }

            function updateTaskCount(column) {
                const count = column.find('.kb-card').length;
                column.find('.task-count').text(`${count} Tasks`);
            }

            function updateBoardMember(member)
            {
                if (!member) return;
                if (Array.isArray(member)) member = member[0];
                const $dropdown = $('#boardMembersDropdown');

                const profile = member.detail?.profile_image ?? null;
                const username = member.username ?? 'U';

                const memberHtml = `
                    <li>
                        <a href="#" class="dropdown-item d-flex align-items-center gap-2">
                            ${profile 
                                ? `<img src="/storage/${profile}" alt="${username}" class="rounded-circle" style="width:32px; height:32px; object-fit:cover;">`
                                : `<div class="rounded-circle bg-warning text-center" style="width:32px; height:32px; line-height:32px; font-weight:600; color:#fff;">
                                    ${username.charAt(0).toUpperCase()}
                                </div>`
                            }
                            <span>${username}</span>
                        </a>
                    </li>
                `;

                $dropdown.append(memberHtml);
            }

            //show input for adding new task
            $(document).off('click', '.initiatePmsAddTaskBtn').on('click', '.initiatePmsAddTaskBtn', function() {
                const btn = $(this);
                const cardId = btn.data('card-id');

                btn.hide();
                const form = $('#inline-task-template .inline-task-form').clone();
                form.find('.btn-save-task').data('card-id', cardId);
                btn.before(form);
                form.find('.inline-task-input').focus();
            });

            $(document).off('click', '.btn-cancel-task').on('click', '.btn-cancel-task', function() {
                const form = $(this).closest('.inline-task-form');
                form.prev('.initiatePmsAddTaskBtn').show();
                form.remove();
            });

            //save new task to db
            $(document).off('click', '.btn-save-task').on('click', '.btn-save-task', function() {
                const btn = $(this);
                if (btn.prop('disabled')) return;

        btn.prop('disabled', true); 
                const cardId = btn.data('card-id');
                const form = btn.closest('.inline-task-form');
                const input = form.find('.inline-task-input');
                const title = input.val().trim();
                const column = form.closest('.kb-column');
                const cardBody = column.find('.kb-column-body');

                if (!title) {
                    btn.prop('disabled', false); 
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
                        hideSpinner(btn);
                        if (response.success) {
                            const addBtn = cardBody.find('.initiatePmsAddTaskBtn');
                            addBtn.before(renderTaskCard(response.data));
                            updateTaskCount(column);
                            addBtn.show();
                            form.remove();
                        } else {
                            btn.prop('disabled', false); 
                            Swal.fire('Error', response.message, 'error');

                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false); 
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
                            if ($(evt.item).hasClass('kb-add-task-btn')) return;
                           const targetColumn = evt.to;
                           const cardId = targetColumn.dataset.cardId;

                            let positions = [];
                            targetColumn.querySelectorAll('.kb-card').forEach((el, index) => {
                            positions.push({
                            task_id: el.dataset.taskId,
                            position: index + 1
                        });
                        });
                        
                        if (
                            evt.from === evt.to &&
                            evt.oldIndex === evt.newIndex
                        ) {
                            return;
                        }

                             Swal.fire({
                                title:'Moving task..',
                                toast:true,
                                position:'top-end',
                                showConfirmButton:false,
                                didOpen: () => {
                                        Swal.showLoading(); // show spinner
                                }
                            });
                            $.ajax({
                                url: `/pms-task-reorder`,
                                type: "POST",
                                data: {
                                    _token: document.querySelector('meta[name="csrf-token"]').content,
                                    card_id: cardId,
                                    positions: positions
                                },
                                success: function(response) {
                                    Swal.close();
                                    if (response.success) {
                                        Swal.fire('Success', response.message,
                                            'success');
                                    } else {
                                        Swal.fire('Error', response.message,
                                            'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.close();
                                    Swal.fire('Error', 'Something went wrong',
                                        'error');
                                    console.error(xhr.responseText);
                                }
                            });
                        }
                    })
                });
            }

            //open modal to fill details for adding new card
            
            $(document).off('click', '#pmsAddCardBtn').on('click', '#pmsAddCardBtn', function() {
                $("#pmsAddNewCardModal").modal('show');
                $('#pmsAddNewCardModal').find('#pms_card_board_id').val(boardId);
            });

            //open modal to edit task
            $(document).off('click', '.pms-task-item').on('click', '.pms-task-item', function() {
                const taskId = $(this).data('task-id');
                const modal = $('#pmsEditTaskModal');
                modal.modal('show');
                modal.data('task-id', taskId);
            });

            //add new member to the opened board
            $(document).off('click', '.add-board-member').on('click', '.add-board-member', function(e) {
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
                            updateBoardMember(response.data);
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
        
}

        $(document).on('click','.deleteCardBtn',function(){
        const cardId= $(this).data('card-id');
        const boardId= $(this).data('board-id');
        const csrf=document.querySelector('meta[name="csrf-token"]').content;
        Swal.fire({
            title:'Are you sure?',
            text:'This will delete the checklist!',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Yes, delete it!'
        }).then((result)=>{
            if(result.isConfirmed){
                $.post(`/card-delete/${cardId}`,
                    { _token:csrf},
                    function(response){
                if (response.success) {
                    Swal.fire('Deleted!', response.message, 'success')
                        .then(() => {
                            
                            location.reload();
                        });

                } else {
                    Swal.fire('Error', response.message, 'error');
                }
                    }
                );
            }
        });
    });