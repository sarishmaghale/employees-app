

function renderComments(comments, container) {
    container.empty();

    if (comments.length > 0) {
        comments.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        comments.forEach(comment => {
            const createdAt = new Date(comment.created_at);
            const formattedDate = formatDate(comment.created_at);
            const commentHtml = `
                <div class="d-flex align-items-start mb-2">
                    <div class="rounded-circle bg-warning text-center me-2"
                        style="width:32px; height:32px; line-height:32px;">
                        ${comment.employee.username.charAt(0) || 'U'}
                    </div>

                    <div>
                        <strong>${comment.employee.username}</strong>
                        ${ comment.comment_type === 0 
                            ? comment.comment 
                            : `commented:<br/> ${comment.comment}`
                        }
                        <br>
                        <small class="text-muted">${formattedDate}</small>
                    </div>
                </div>
                `;

            container.append(commentHtml);
        });
    }
}

function renderCheckList(checklists, append = false) {
    const container = $('#taskChecklistContainer');
    if (!append) container.empty();
    if (!checklists) return;

    if (!Array.isArray(checklists)) checklists = [checklists];

    let checklistHtml = '';

    if (checklists && checklists.length > 0) {
        checklists.forEach(checklist => {
             const totalItems = checklist.items?.length || 0;
        const completedItems = checklist.items?.filter(item => item.isCompleted).length || 0;
        const percentage = totalItems ? Math.round((completedItems / totalItems) * 100) : 0;
            checklistHtml += `
                <div class="mb-3 checklist-block" data-id="${checklist.id}">
                    <h6>${checklist.title}</h6>
                     <div class="text-end small text-muted checklist-percentage">
                    ${percentage}% completed
                </div>
                    <div class="checklist-items">
                        ${checklist.items && checklist.items.length > 0
                            ? checklist.items.map(item => `
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" id="checkItem${item.id}" ${item.isCompleted ? 'checked' : ''}>
                                <label class="form-check-label">${item.item_title}</label>
                            </div>
                        `).join('')
                            : ''
                        }
                    </div>

                    <button type="button" class="btn btn-link btn-sm addCheckboxBtn">
                        <i class="fas fa-plus"></i> Add Checkbox
                    </button>
                </div>
                `;
        });
    }
    container.append(checklistHtml);
}


//activates after save button from edit page- update the details
$(document).on('click', '#pmsUpdateTaskBtn', function(e) {
        e.preventDefault();
        const btn = this;
        showSpinner(btn);
        const task = $('#pms_edit_task_id').val();
        const form = document.getElementById('pmsEditTaskForm');
        const formData = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrfToken);

        $('#taskChecklistContainer .checklist-block .checklist-items .form-check input[type="checkbox"]').each(
            function(index) {
                const itemId = $(this).attr('id')?.replace('checkItem', '');
                const completed = $(this).is(':checked') ? 1 : 0;

                if (itemId) {
                    formData.append(`checklist_items[${index}][id]`, itemId);
                    formData.append(`checklist_items[${index}][completed]`, completed);
                }
            });

        $.ajax({
            url: `/pms-update-task/${task}`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner(btn);
                Swal.fire({
                    title: response.success ? 'Success' : 'Error',
                    text: response.message,
                    icon: response.success ? 'success' : 'error',
                });
            },
            error: function(xhr) {
                hideSpinner(btn);
                Swal.fire('Error', 'Something went wrong', 'error');
                console.error('Error: ', xhr.responseText);
            }
        })
});

//saving comment to db
$(document).on('submit', '#pmsTaskCommentForm', function(e) {
    e.preventDefault();

    let comment = $('#commentInput').val().trim();
    if (comment === "") {
        $('#commentInput').addClass('is-invalid').focus();
        return;
    }

    const btn = document.getElementById('postCommentBtn');
    showSpinner(btn);
    const container = $('#commentsContainer');
    const task = $('#pms_edit_task_id').val();

    $.ajax({
        url: `/pms-task-comment`,
        method: "POST",
        data: {
                _token: document.querySelector('meta[name="csrf-token"]').content,
            comment: comment,
            task_id: task
        },
        success: function(response) {
            $('#commentInput').removeClass('is-invalid');
            hideSpinner(btn);
            if (response.success) {
                $('#commentInput').val('');
                renderComments(response.data, container);
            } else Swal.fire('Error', response.message, 'error');
        },
        error: function(xhr) {
            hideSpinner(btn);
            Swal.fire("Error", 'Something went wrong', 'error');
            console.error('Error:', xhr.responseText);
        }
    })

});

//saving checklist for the task 
$(document).on('submit', '#pmsAddchecklistForm', function(e) {
    e.preventDefault();
    const title = $('#checklistTitleInput').val().trim();

    if (!title) {
        $('#checklistTitleInput').addClass('is-invalid').focus();
        return;
    }
    const taskId = $('#pms_edit_task_id').val();

    $.ajax({
        url: "/pms-checklist",
        method: "POST",
        data: {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            title: title,
            task_id: taskId
        },
        success: function(response) {
            console.log(response.data);
            if (response.success) {
                $('#pmsAddChecklistModal').modal('hide');
                renderCheckList(response.data, true);
            } else Swal.fire('Error', response.message, 'error');
        },
        error: function(xhr) {
            Swal.fire('Error', 'Something went wrong', 'error');
            console.error('Error', xhr.responseText);
        }
    });
});

//show field to add checkbox for checklist
$(document).on('click', '.addCheckboxBtn', function() {
    const container = $(this).siblings('.checklist-items');
    const uniqueId = Date.now();

    container.append(`
            <div class="form-check mb-1 d-flex align-items-center gap-2" data-value="">
                <input class="form-check-input" type="checkbox" id="checkItem${uniqueId}">
                <input type="text" class="form-control form-control-sm checklist-item-input" placeholder="Item name">
                <button type="button" class="btn btn-sm btn-success saveCheckboxBtn">Save</button>
            </div>
        `);
});

//save checkbox item for checklist in db
$(document).on('click', '.saveCheckboxBtn', function() {
    const btn = this;
    const parent = $(this).closest('.form-check');
    const input = parent.find('.checklist-item-input');
    const value = input.val().trim();

    if (!value) {
        input.addClass('is-invalid').focus();
        return;
    }
    showSpinner(btn);
    const checklistId = parent.closest('.checklist-block').data('id');

    $.ajax({
        url: "/pms-checklist-item",
        method: "POST",
        data: {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            checklist_id: checklistId,
            title: value
        },
        success: function(response) {
            hideSpinner(btn);
            if (response.success) {
                const checkbox = parent.find('input[type="checkbox"]');
                parent.attr('data-value', value);
                parent.html(`
            <input class="form-check-input" type="checkbox" id="checkItem${response.data.id}" ${response.data.completed ? 'checked' : ''}>
            <label class="form-check-label">${response.data.item_title}</label>
        `);
            } else Swal.fire('Error', response.message, 'error');
        },
        error: function(xhr) {
            hideSpinner(btn);
            Swal.fire('Error', 'Something went wrong', 'error');
            console.error('Error:', xhr.responseText);
        }
    });

});

$('#pmsEditTaskModal').on('hidden.bs.modal', function() {
    $(this).find('#pmsEditTaskForm')[0].reset();
    $('#commentInput').removeClass('is-invalid').val('');
    $('#pmsEditTaskTitle').html('<i class="far fa-circle"></i> Details');
    $('#taskChecklistContainer').empty();
    $('#commentsContainer').empty();
});