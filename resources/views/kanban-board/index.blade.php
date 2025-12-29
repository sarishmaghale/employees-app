@extends('layout')
@push('styles')
    <style>
        #selectedUser.has-value {
            border: 2px solid var(--primary);
            background: #f0f4ff;
            color: var(--primary-dark);
            font-weight: 600;
            transition: all 0.3s ease;
        }
    </style>
@endpush

@section('content')
    <h1 class="kb-title">Manage your work</h1>

    <div class="kb-filters-wrapper">
        <div class="kb-filters-wrapper-inner">
            <!-- Left Side - Filter Buttons -->
            <div class="kb-filters">
                @forelse(getTaskCategories() as $category)
                    <button class="kb-filter-btn" data-category-id="{{ $category->id }}"
                        data-category-name="{{ $category->category_name }}">
                        <span class="kb-filter-dot" style="background: {{ $category->color }}">
                        </span>
                        {{ $category->category_name }}
                    </button>
                @empty
                @endforelse

                @if (Auth::user()->role === 'admin')
                    <div class="kb-employee-select-wrapper">
                        <select id="selectedUser" class="kb-employee-select">
                            @forelse(getEmployees() as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->username }}</option>
                            @empty
                                <option value="">No employees found</option>
                            @endforelse
                        </select>
                    </div>
                @endif
            </div>

            <!-- Right Side - Action Buttons -->
            <div class="kb-add-card-top">
                <button class="kb-add-card-global">+ Add Card</button>
            </div>
        </div>
    </div>
    <div class="kb-board">

    </div>
    @include('kanban-board.add-kanban-card-partial')
    @include('kanban-board.add-board-task-partial')
    @include('partial-views.task-details')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let userId = @json(Auth::id());
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
            $('.kb-filter-btn').first().trigger('click');

            function loadBoardData(categoryId) {
                $.ajax({
                    url: `/kanban-board/${categoryId}?userId=${userId}`,
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
                                                    <button class="kb-add-task-btn" data-status-id="${col.id}" data-category-id="${activeCategoryId}">
                                                    + Add Task
                                                </button>
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

            $('#selectedUser').on('change', function() {
                userId = $(this).val();
                console.log('New selected user:', userId);
                $(this).addClass('has-value');
                if (activeCategoryId) {
                    loadBoardData(activeCategoryId);
                } else {
                    $('.kb-filter-btn').first().trigger('click');
                }
            });

            $('.kb-add-card-global').on('click', function() {
                $('#status_emp_id').val(userId);
                $('#addNewStatusCardModal').modal('show');
            });

            $(document).on('click', '.kb-add-task-btn', function() {
                const status_id = $(this).data('status-id');
                const categoryId = $(this).data('category-id');
                $('#addTaskToBoardModal').data('status-id', status_id);
                const categoryName = $('#addTaskToBoardModal').data('category-name');
                $('.modal-title').text(categoryName + ' Tasks List');
                $.ajax({
                    url: `/board-tasks/${categoryId}?userId=${userId}`,
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
            });
        })
    </script>
@endpush
