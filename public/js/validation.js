function handleValidationErrors(xhr,formSelector)
{
    const errors=xhr.responseJSON.errors;
    $(formSelector + ' .form-control').removeClass('is-invalid');
    $(formSelector + ' .invalid-feedback').remove();
    $.each(errors,function(field,messages){
        const input=$(formSelector + ' [name="' + field + '"]');
        input.addClass('is-invalid');
        if(messages.length>0){
            input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
        }
    });
    return true;
}