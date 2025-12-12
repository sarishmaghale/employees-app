export function saveNewTask(formData,calendar,modalForm)
{
    $.ajax({
        url: '/tasks',
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire('Success', response.message, 'success');
                $(modalForm).modal('hide');
                calendar.refetchEvents();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) handleValidationErrors(xhr, '#taskForm');
            else console.error('Error:', xhr.responseText);
        }
    });
}

export function updateExistingTask(formData,calendar,modalForm, editEventId)
{
     $.ajax({
        url: `/task/update/${editEventId}`,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire('Success', response.message, 'success');
                $(modalForm).modal('hide');
                calendar.refetchEvents();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) handleValidationErrors(xhr, '#taskForm');
            else console.error('Error:', xhr.responseText);
        }
    });
}

export function deleteTask(csrf,editEventId,calendar,modalForm)
{
$.ajax({
            url: `/task/${editEventId}`,
            method: "DELETE",
            headers: {
                'X-CSRF-TOKEN': csrf
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Deleted!', response.message, 'success');
                    $(modalForm).modal('hide');
                    calendar.refetchEvents();
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
            }
        });
}