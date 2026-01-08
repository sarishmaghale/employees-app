function post(url, data, success, error)
{
    $.ajax({
        url:url,
        method:"POST",
        data:data,
        headers:{
            'X-CSRD-TOKEN':$('meta[name="csrf-token]').attr('content')
        },
        success:success,
        error:error||function(xhr) {console.error(xhr.responseText);

        }
    });
}