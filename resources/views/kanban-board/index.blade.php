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
                            data-color="{{ $category->color }}">
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const userId = @json(Auth::id());
            console.log('LoggedUser:', userId);
            $('.kb-filter-btn').on('click', function() {
                $('.kb-filter-btn').removeClass('active');
                $(this).addClass('active');
                activeCategoryId = $(this).data('category-id');
                $.ajax({
                    url: `/kanban-board/${activeCategoryId}`,
                    type: "GET",
                    success: function(response) {
                        if (response.success) {
                            let board = $('.kb-board');
                            board.empty();
                            let columns = response.data;
                            console.log(columns);
                            columns.forEach(col => {
                                let colHtml = ` <div class="kb-column">
                            <div class="kb-column-header">
                                <h2>${col.status.name}</h2>
                           <span> 3 Tasks </span>
                            </div>
                          </div> `;
                                board.append(colHtml);
                            });

                        };
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            });

            $('.kb-add-card-global').on('click', function() {
                $('#status_emp_id').val(userId);
                $('#addNewStatusCardModal').modal('show');
            })
        })
    </script>
@endpush
