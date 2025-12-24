@extends('layout')

@section('content')
    <div class="kb-wrapper">
        <div class="kb-container">

            <h1 class="kb-title">Manage your work</h1>

            <!-- Category Filters -->
            <div class="kb-filters-wrapper">

                <div class="kb-filters">

                    @foreach (getTaskCategories() as $category)
                        <button class="kb-filter-btn" data-category-id="{{ $category->id }}"
                            data-category-name="{{ $category->category_name }}">
                            <span class="kb-filter-dot" style="background: {{ $category->color }}"></span>
                            {{ $category->category_name }}
                        </button>
                    @endforeach
                </div>
                <div class="kb-add-card-top">
                    <button class="kb-add-card-global">+ Add Card</button>
                </div>
            </div>
            <!-- Kanban Board -->
            <div class="kb-board">

                <!-- TO DO -->
                {{-- <div class="kb-column todo">
                    <div class="kb-column-header">
                        <h2>To Do</h2>
                        <span>3 tasks</span>
                    </div>

                    <div class="kb-column-body">
                        <div class="kb-card todo">
                            <h3>Design new landing page</h3>
                            <p>Create wireframes and mockups</p>
                            <div class="kb-card-meta">
                                <span class="kb-tag">Design</span>
                                <span class="kb-date">Due: Dec 25</span>
                            </div>
                        </div>
                    </div>
                </div> --}}

            </div>
        </div>
    </div>
    @include('kanban-board.add-kanban-card-partial')
    @include('kanban-board.add-board-task-partial')
    @include('partial-views.task-details')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const userId = @json(Auth::id());
            console.log('LoggedUser:', userId);
            let activeCategoryId = null;
            $('.kb-filter-btn').on('click', function() {
                $('.kb-filter-btn').removeClass('active');
                $(this).addClass('active');
                activeCategoryId = $(this).data('category-id');
                let categoryName = $(this).data('category-name');
                $('#addTaskToBoardModal').data('category-name', categoryName);
                loadBoardData(activeCategoryId);
            });

            function loadBoardData(categoryId) {
                $.ajax({
                    url: `/kanban-board/${categoryId}`,
                    type: "GET",
                    success: function(response) {
                        if (response.success) {
                            let board = $('.kb-board');
                            board.empty();
                            let columns = response.data;
                            console.log(columns);

                            columns.forEach(col => {
                                let colHtml = `
                                    <div class="kb-column" >
                                        <div class="kb-column-header">
                                            <div class="kb-column-header-top">
                                                <h2>${col.status.name}</h2>
                                                <button class="kb-add-task-btn" data-status-id="${col.id}" data-category-id="${activeCategoryId}">
                                                    + Add Task
                                                </button>
                                            </div>
                                            <span>${col.tasks ? col.tasks.length : 0} Tasks</span>
                                        </div>
                                        <div class="kb-column-body" data-link-id="${col.id}">
                                                ${col.tasks && col.tasks.length > 0 ? col.tasks.map(task => `
                                                <div class="kb-card openTaskDetail" data-task-id="${task.id}">
                                                    <h3>${task.title}</h3>
                                                    <div class="kb-card-meta">
                                                        <span class="kb-tag">${task.badge}</span>
                                                        <span class="kb-date">Due: ${task.end}</span>
                                                    </div>
                                                </div>
                                            `).join('') : ''}
</div>
                                    </div>`;
                                board.append(colHtml);
                            });
                            enableDragDrop();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });

            };

            $('.kb-add-card-global').on('click', function() {
                $('#status_emp_id').val(userId);
                $('#addNewStatusCardModal').modal('show');
            })

            $(document).on('click', '.kb-add-task-btn', function() {
                const status_id = $(this).data('status-id');
                const categoryId = $(this).data('category-id');
                $('#addTaskToBoardModal').data('status-id', status_id);
                const categoryName = $('#addTaskToBoardModal').data('category-name');
                $('.modal-title').text(categoryName + ' Tasks List');
                $.ajax({
                    url: `/board-tasks/${ categoryId}`,
                    type: "GET",
                    success: function(response) {
                        const taskList = $('#board-taskList');
                        taskList.empty();
                        let tasks = response.data;
                        if (tasks.length === 0) taskList.append(
                            `<li> No tasks assigned</li>`);
                        else {
                            tasks.forEach(task => {
                                taskList.append(` <li class = "list-group-item task-item"
                                    data-task-id = "${task.id}" >
                                        ${task.title} 
                                        </li>`);
                            });
                        }

                        $('#addTaskToBoardModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                })
            });

            document.addEventListener('board.refresh', function() {
                loadBoardData(activeCategoryId);
            });

            function enableDragDrop() {
                $('.kb-column-body').each(function() {

                    new Sortable(this, {
                        group: 'tasks',
                        animation: 150,
                        onEnd: function(event) {
                            const taskId = $(event.item).data('task-id');
                            const newStatusId = $(event.to).data('link-id');
                            console.log('tsk', taskId)
                            console.log('status', newStatusId)
                            $.ajax({
                                url: `/board-task-move`,
                                type: "POST",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr(
                                        'content'),
                                    statusId: newStatusId,
                                    taskId: taskId
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire('Success', response.message,
                                            'success');

                                    } else Swal.fire('Error', response.message,
                                        'error');
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', 'Something went wrong',
                                        'error');
                                    console.error('Error:', xhr.responseText);
                                }
                            })
                        }
                    })

                })
            }

            $(document).on('click', '.openTaskDetail', function() {
                const taskId = $(this).data('task-id');
                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: "GET",
                    dataType: 'json',
                    success: function(response) {
                        $('#taskTitle').text(response.data.title);
                        $('#taskType').text(response.data.task_category.category_name);
                        $('#taskStart').text(response.data.start);
                        $('#taskEnd').text(response.data.end);
                        $('#taskBadge').text(response.data.badge);
                        const taskModal = new bootstrap.Modal(document.getElementById(
                            'taskDetailsModal'));
                        taskModal.show();
                    },
                    error: function(xhr) {
                        console.error('Error', xhr.responseText);
                    }
                })
            })
        })
    </script>
@endpush
