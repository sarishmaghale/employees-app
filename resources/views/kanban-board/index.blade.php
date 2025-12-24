@extends('layout')

@section('content')
    <div class="kb-wrapper">
        <div class="kb-container">

            <h1 class="kb-title">Project Board</h1>

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

                <!-- IN PROGRESS -->
                {{-- <div class="kb-column in-progress">
                    <div class="kb-column-header">
                        <h2>In Progress</h2>
                        <span>2 tasks</span>
                    </div>

                    <div class="kb-column-body">
                        <div class="kb-card in-progress">
                            <h3>Update documentation</h3>
                            <p>Review and update API docs</p>
                            <div class="kb-card-meta">
                                <span class="kb-tag warning">Documentation</span>
                                <span class="kb-date">Due: Dec 28</span>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- DONE -->
                {{-- <div class="kb-column done">
                    <div class="kb-column-header">
                        <h2>Done</h2>
                        <span>4 tasks</span>
                    </div>

                    <div class="kb-column-body">
                        <div class="kb-card done">
                            <h3>Write unit tests</h3>
                            <p>Add test coverage</p>
                            <div class="kb-card-meta">
                                <span class="kb-tag success">Testing</span>
                                <span class="kb-date">Completed: Dec 21</span>
                            </div>
                        </div>
                    </div>
                </div> --}}

            </div>
        </div>
    </div>
    @include('partial-views.add-kanban-card-partial')
    @include('partial-views.add-board-task-partial')
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
                                    <div class="kb-column">
                                        <div class="kb-column-header">
                                            <div class="kb-column-header-top">
                                                <h2>${col.status.name}</h2>
                                                <button class="kb-add-task-btn" data-status-id="${col.id}" data-category-id="${activeCategoryId}">
                                                    + Add Task
                                                </button>
                                            </div>
                                            <span>${col.tasks ? col.tasks.length : 0} Tasks</span>
                                        </div>
                                        <div class="kb-column-body">
                                                ${col.tasks && col.tasks.length > 0 ? col.tasks.map(task => `
                                                    <div class="kb-card">
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
                        response.data.forEach(task => {
                            taskList.append(` <li class = "list-group-item task-item"
                                    data-task-id = "${task.id}" >
                                        ${task.title} 
                                        </li>`);
                        });
                        $('#addTaskToBoardModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                })
            });
            document.addEventListener('board.refresh', function() {
                loadBoardData(activeCategoryId);
            })


        })
    </script>
@endpush
