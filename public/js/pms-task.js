
function initializeTaskDetails(taskId,modal)
{
      modal.find('#pms_edit_task_id').val(taskId);

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

                renderComments(task.comments ?? []);
                renderCheckList(task.checklists ?? []);
                renderAssignedEmployee(task.assigned_employees ?? []);
                populateTaskLabels(task.labels ?? []);
                console.log(task.files);
                renderTaskFiles(task.files ?? []);
            },
            error: function(xhr) {
                Swal.fire("Error", "Failed to load task details", "error");
                console.error(xhr.responseText);
            }
        });

    //DB: update the details
    $(document).off('click', '#pmsUpdateTaskBtn').on('click', '#pmsUpdateTaskBtn', function(e) {
            e.preventDefault();
            const btn = this;
            showSpinner(btn);

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

            let selectedLabelIds = [];
                $('.add-task-label-checkbox:checked').each(function() {
                    selectedLabelIds.push($(this).val());
            });
            selectedLabelIds.forEach((id, index) => {
                formData.append(`labels[${index}]`, id);
            });

            $.ajax({
                url: `/pms-update-task/${taskId}`,
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

    //DB: saving comment 
    $(document).off('click', '#pmsTaskCommentForm').on('submit', '#pmsTaskCommentForm', function(e) {
        e.preventDefault();

        let comment = $('#commentInput').val().trim();
        if (comment === "") {
            $('#commentInput').addClass('is-invalid').focus();
            return;
        }

        const btn = document.getElementById('postCommentBtn');
        showSpinner(btn);

        $.ajax({
            url: `/pms-task-comment`,
            method: "POST",
            data: {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                comment: comment,
                task_id: taskId
            },
            success: function(response) {
                $('#commentInput').removeClass('is-invalid');
                hideSpinner(btn);
                if (response.success) {
                    $('#commentInput').val('');
                    renderComments(response.data, true);
                } else Swal.fire('Error', response.message, 'error');
            },
            error: function(xhr) {
                hideSpinner(btn);
                Swal.fire("Error", 'Something went wrong', 'error');
                console.error('Error:', xhr.responseText);
            }
        })

    });

 const fileInput = $('#pmsTaskAttachment');
const container = $('#selectedFilesContainer');

fileInput.on('change', function() {
    if (this.files.length === 0) return;

    const file = this.files[0];
    const tempId = `new-${Date.now()}`; // temporary ID for UI

    // Show uploading placeholder
    const uploadingHtml = `
        <div class="task-file d-flex align-items-center justify-content-between mb-2" id="${tempId}">
            <div class="d-flex align-items-center gap-2">
                <span>Uploading ${file.name}...</span>
            </div>
        </div>
    `;
    container.append(uploadingHtml);
    container.show();

    // Prepare FormData
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    formData.append('task_id', $('#pms_edit_task_id').val());

    $.ajax({
        url: '/pms-task-upload-file', 
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $(`#${tempId}`).remove();

            if (response.success) {
                const savedFile = response.data;
                renderTaskFiles([savedFile], true); 
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            $(`#${tempId}`).remove();
            Swal.fire('Error', 'File upload failed', 'error');
            console.error(xhr.responseText);
        }
    });
});

    
 
    //DB: saving checklist for the task 
    $(document).off('click', '#pmsAddChecklistForm').on('submit', '#pmsAddchecklistForm', function(e) {
        e.preventDefault();
        const title = $('#checklistTitleInput').val().trim();

        if (!title) {
            $('#checklistTitleInput').addClass('is-invalid').focus();
            return;
        }

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

    //Frontend: show field to add checkbox for checklist
    $(document).off('click', '.addCheckboxBtn').on('click', '.addCheckboxBtn', function() {
        const container = $(this).siblings('.checklist-items');
        const uniqueId = Date.now();

        container.append(`
                <div class="form-check mb-1 d-flex align-items-center gap-2" data-value="">
                    <input class="form-check-input" type="checkbox" id="checkItem${uniqueId}">
                    <input type="text" class="form-control form-control-sm checklist-item-input" placeholder="Item name">
                    <button type="button" class="btn btn-sm btn-success saveCheckboxBtn">Add</button>
                </div>
            `);
    });

    //DB: save checkbox item for checklist
    $(document).off('click', '.saveCheckboxBtn').on('click', '.saveCheckboxBtn', function() {
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

    //DB: saving new employee as member to task
    $(document).off('click', '.add-task-member').on('click', '.add-task-member', function(e) {
                    e.preventDefault();
                    let employeeId = $(this).data('id');
                    $.ajax({
                        url: `/pms-task/${taskId}/add-member`,
                        type: "POST",
                        data: {
                            employee_id: employeeId,
                            _token: document.querySelector('meta[name="csrf-token"]').content,
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success');
                                const members= response.data;
                                renderAssignedEmployee(members,true);
                            } else {
                                Swal.fire('Error', response.message, 'warning');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Something went wrong', 'error');
                            console.error('Error:', xhr.responseText);
                        }
                    })
    });
}

    function renderComments(comments, append=false) {
         const container = $('#commentsContainer');
        if (!append) container.empty();
        if (!comments) return;
        if (!Array.isArray(comments)) comments = [comments];
            let html = '';
            comments.forEach(comment => {
                const formattedDate = formatDate(comment.created_at);
                html += `
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

            });
         if (append) container.prepend(html);
        else  container.append(html);   
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

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="mb-0">${checklist.title}</h6>
                        <button type="button" class="btn btn-link btn-sm p-0 text-danger deleteChecklistBtn" data-checklist-id="${checklist.id}">
                            Delete Item
                        </button>
                    </div>

                    <div class="small text-muted mb-2">
                        ${percentage}% completed
                    </div>

                    <div class="checklist-items">
                        ${checklist.items && checklist.items.length > 0
                            ? checklist.items.map(item => `
                                <div class="form-check mb-1 d-flex justify-content-between align-items-center">
                                    <div>
                                        <input class="form-check-input" type="checkbox" id="checkItem${item.id}" ${item.isCompleted ? 'checked' : ''}>
                                        <label class="form-check-label">${item.item_title}</label>
                                    </div>
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
        if (append) container.prepend(checklistHtml); 
        else container.append(checklistHtml);  
    }

    function renderAssignedEmployee(employees,append=false)
    {
        const container = $('#assignedEmployeesContainer');
        if (!append) container.empty();
         if (!Array.isArray(employees)) employees = [employees];
        if(employees && employees.length>0)
        {
            employees.forEach(employee=>{
                const profile=employee.detail?.profile_image??null;
                const username= employee.username??'U';
                const avatarHtml= profile
                ? `<img src="/storage/${profile}" alt="${username}">`
                : `<span>${username.charAt(0).toUpperCase()}</span>`;
                container.append(`
                <div class="member-avatar" title="${username}">
                    ${avatarHtml}
                </div>
            `);
            });
        }

    }

    function populateTaskLabels(taskLabels) {
        $('.task-label-checkbox').prop('checked', false);

        const labelIds = taskLabels.map(label => label.id);

        labelIds.forEach(id => {
            $(`#label${id}`).prop('checked', true);
        });
    }

    function renderTaskFiles(files, append = false) { 
    const container = $('#selectedFilesContainer');

    if (!append) container.empty(); 

     if (!files || files.length === 0) {
        container.hide(); 
        return;
    }
 container.show();
    files.forEach(file => {
        const filePath = file.file_path;
        const fileName=file.file_name;
        const fileId = file.id;
        const fileExtension = filePath.split('.').pop().toLowerCase();

        let fileHtml = '';

        if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp'].includes(fileExtension)) {
            // Image preview
            fileHtml = `<img src="/storage/${filePath}" alt="${fileName}" class="task-img-preview me-2" style="max-height:80px; border-radius:4px;">`;
        } else if (fileExtension === 'pdf') {
            // PDF icon + link
            fileHtml = `
                <a href="/storage/${filePath}" target="_blank" class="d-flex align-items-center gap-1">
                    <i class="fas fa-file-pdf text-danger"></i> ${fileName}
                </a>`;
        } else {
            // Generic file icon + link
            fileHtml = `
                <a href="/storage/${filePath}" target="_blank" class="d-flex align-items-center gap-1">
                    <i class="fas fa-file text-secondary"></i> ${fileName}
                </a>`;
        }

        container.append(`
            <div class="task-file d-flex align-items-center justify-content-between mb-2" data-id="${fileId}">
                <div class="d-flex align-items-center">
                    ${fileHtml}
                </div>
                <div>
                    <button type="button" class="btn btn-link btn-sm p-0 text-danger remove-file-btn">Remove</button>
                </div>
            </div>
        `);
    });
    }

   $(document).on('click', '.remove-file-btn', function () {
    const fileRow = $(this).closest('.task-file');
    const fileId = fileRow.data('id');

    Swal.fire({
        title: 'Remove file?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it',
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `/pms-task-file/${fileId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (!res.success) {
                    return Swal.fire('Error', res.message, 'error');
                }

                // Remove from UI
                fileRow.fadeOut(200, function () {
                    $(this).remove();
                });

                Swal.fire('Deleted!', res.message, 'success');
            },
            error: function (xhr) {
                Swal.fire('Error', 'Failed to remove file', 'error');
                console.error(xhr.responseText);
            }
        });
    });
});



    $(document).on('click','.deleteChecklistBtn',function(){
        const checklistId= $(this).data('checklist-id');
        const csrf=document.querySelector('meta[name="csrf-token"]').content;
        Swal.fire({
            title:'Are you sure?',
            text:'This will delete the checklist!',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Yes, delete it!'
        }).then((result)=>{
            if(result.isConfirmed){
                $.post(`/checklist-delete/${checklistId}`,
                    { _token:csrf},
                    function(response){
                if (response.success) {
                    $(`.checklist-block[data-id="${checklistId}"]`).remove();
                    Swal.fire('Deleted!', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
                    }
                );
            }
        });
    });

        $('#pmsEditTaskModal').on('hidden.bs.modal', function() {
        $(this).find('#pmsEditTaskForm')[0].reset();
        $('#commentInput').removeClass('is-invalid').val('');
        $('#pmsEditTaskTitle').html('<i class="far fa-circle"></i> Details');
        $('#taskChecklistContainer').empty();
        $('#commentsContainer').empty();
        $('#selectedFilesContainer').empty();
    });
