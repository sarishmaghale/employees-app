$(document).ready(function(){

const notificationMenu = document.getElementById('notificationMenu');
const notificationBtn = notificationMenu?.querySelector('.notification-btn');

    if (notificationBtn) {
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationMenu.classList.toggle('active');
            // Close user menu if open
            document.getElementById('userMenu')?.classList.remove('active');
        });
    }
$(document).on('click', '.notification-item.unread', function(event) {
    event.preventDefault();
    markAsRead(event, this);
});

$('#markAllRead').on('click', function() {
    $.ajax({
        url:`/notification/mark-all-read`,
        type:"POST",
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(response){
            if(response.success){
                $('.notification-item.unread').removeClass('unread');
                Swal.fire('Success',response.message,'success');
            }
        },error:function(xhr){
            Swal.fire('Error','Something went wrong','error');
            console.log('Error:',xhr.responseText);
        }

    });
})

    function markAsRead(e, element)
    {
        e.preventDefault();
        const $item = $(element);
        const taskType = $item.data('task-type');
        const taskId = $item.data('task-id');
        const url = $item.data('url');
        const redirectUrl=$item.data('task-url');

        $.ajax({
            url:url,
            type:"POST",
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function(response){
                console.log('activate read');
                if(response.success)
                {
                    $item.removeClass('unread');
                    updateNotificationBadge(-1);
                    if(taskType==='task_assigned'|| taskType==='task_updated'){
                        openTaskDetailModal(taskId);
                    }
                    // window.location.href=redirectUrl;
                }
            },error:function(xhr){
                console.error('Error: ',xhr.responseText);
            }
            
        })
    }

    function updateNotificationBadge(change)
    {
        const badge=$('.notification-badge');
        if(badge.length){
            const currentCount=parseInt(badge.text());
            const newCount=currentCount+change;
            if(newCount<=0) badge.remove();
            else badge.text(newCount);
        }
    }

    function openTaskDetailModal(taskId)
    {
        if(!taskId) return;
        $.ajax({
            url:`/tasks/${taskId}`,
            type:"GET",
            dataType:'json',
            success:function(response)
            {
                $('#taskTitle').text(response.data.title);
                
                $('#taskType').text(response.data.task_category.category_name);
                $('#taskStart').text(response.data.start);
                $('#taskEnd').text(response.data.end);
                $('#taskBadge').text(response.data.badge);
                const taskModal = new bootstrap.Modal(document.getElementById('taskDetailsModal'));
                taskModal.show();
            }, error:function(xhr)
            {
                console.error('Error', xhr.responseText);
            }
        })
    }
});