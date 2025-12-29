@extends('layout')

@section('content')
    <div class="kb-filters-wrapper">
        <div class="kb-filters-wrapper-inner">
            <!-- Left Side - Filter Buttons -->
            <div class="kb-filters">
                {{ $board->board_name }}
            </div>

            <!-- Right Side - Action Buttons -->
            <div class="kb-add-card-top">
                <button class="kb-add-card-global" id="pmsAddCardBtn">+ Add Card</button>
            </div>
        </div>
    </div>

    <div class="kb-board">
        @forelse($board->cards as $card)
            <div class="kb-column" data-card-id="{{ $card->id }}">
                <div class="kb-column-header">
                    <div class="kb-column-header-top">
                        <h2>{{ $card->title }}</h2>
                    </div>
                    <span class="task-count">{{ $card->tasks->count() }}
                        Tasks</span>

                </div>
                <div class="kb-column-body" data-card-id="{{ $card->id }}">
                    @foreach ($card->tasks as $task)
                        <div class="kb-card pms-task-item" data-task-id="{{ $task->id }}">
                            <h3>{{ $task->title }}</h3>
                            <div class="kb-card-meta">
                                <span class="kb-tag"></span>
                                <span class="kb-date">Due: {{ $task->end_date ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @endforeach
                    <button class="kb-add-task-btn initiatePmsAddTaskBtn" data-card-id="{{ $card->id }}">
                        + Add Task
                    </button>
                </div>
            </div>
        @empty
            No card yet
        @endforelse
    </div>
    @include('pms.partial-edit-task')
    @include('pms.partial-add-card', ['boardId' => $board->id])

    <div id="inline-task-template" style="display:none;">
        <div class="inline-task-form" style="margin-top:6px;">
            <input type="text" name="title" class="form-control inline-task-input" style="margin-bottom:6px;"
                placeholder="Task title" />
            <button type="button" class="btn btn-primary btn-save-task">Save</button>
            <button type="button" class="btn btn-secondary btn-cancel-task">Cancel</button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const boardId = {{ $board->id }};


            function renderTaskCard(task) {
                return (
                    '<div class="kb-card pms-task-item" data-task-id="' + task.id + '">' +
                    '<h3>' + task.title + '</h3>' +
                    '<div class="kb-card-meta">' +
                    '<span class="kb-tag"></span>' +
                    '<span class="kb-date">Due: ' + (task.end_date || 'N/A') + '</span>' +
                    '</div>' +
                    '</div>'
                );
            }

            function updateTaskCount(column) {
                const count = column.find('.kb-card').length;
                column.find('.task-count').text(`${count} Tasks`);
            }

            $(document).on('click', '.initiatePmsAddTaskBtn', function() {
                const btn = $(this);
                const cardId = btn.data('card-id');

                btn.hide();
                const form = $('#inline-task-template .inline-task-form').clone();
                form.find('.btn-save-task').data('card-id', cardId);
                btn.before(form);
                form.find('.inline-task-input').focus();
            });

            $(document).on('click', '.btn-cancel-task', function() {
                const form = $(this).closest('.inline-task-form');
                form.prev('.initiatePmsAddTaskBtn').show();
                form.remove();
            });

            $(document).on('click', '.btn-save-task', function() {
                const btn = $(this);
                const cardId = btn.data('card-id');
                const form = btn.closest('.inline-task-form');
                const input = form.find('.inline-task-input');
                const title = input.val().trim();
                const column = form.closest('.kb-column');
                const cardBody = column.find('.kb-column-body');
                const taskCount = column.find('.task-count');
                if (!title) {
                    input.focus();
                    return;
                }


                $.ajax({
                    url: `{{ route('pms-task.store') }}`,
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        title: title,
                        card_id: cardId
                    },
                    success: function(response) {
                        if (response.success) {
                            const addBtn = cardBody.find('.initiatePmsAddTaskBtn');
                            addBtn.before(renderTaskCard(response.data));
                            updateTaskCount(column);
                            addBtn.show();
                            form.remove();
                        } else {
                            Swal.fire('Error', response.message, 'error');

                        }
                    },
                    error: function(xhr) {

                        if (xhr.status === 422) handleValidationErrors(xhr, form);
                        else {
                            Swal.fire('Error', 'Something went wrong', 'error');
                            console.error('Error:' + xhr.responseText);
                        }
                    }
                });
            });

            function enableTaskDragOver() {


                $('.kb-column-body').each(function() {
                    new Sortable(this, {
                        group: 'cardTasks',
                        animation: 150,
                        handle: '.kb-card',
                        filter: '.kb-add-task-btn',
                        onEnd: function(evt) {
                            const taskId = $(evt.item).data('task-id');
                            const newCardId = $(evt.to).data('card-id');
                            const newPosition = $(evt.to).children('.kb-card').index(evt.item) +
                                1;
                            console.log('function wotking');
                            $.ajax({

                                url: `{{ route('pms-task.move') }}`,
                                type: "POST",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr(
                                        'content'),
                                    task_id: taskId,
                                    new_card_id: newCardId,
                                    position: newPosition
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire('Success', response.message,
                                            'success');
                                    } else {
                                        Swal.fire('Error', response.message,
                                            'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', 'Something went wrong',
                                        'error');
                                    console.error(xhr.responseText);
                                }
                            });
                        }
                    })
                })
            }

            $(document).on('click', '#pmsAddCardBtn', function() {
                $("#pmsAddNewCardModal").modal('show');
                $('#pmsAddNewCardModal').find('#pms_card_board_id').val(boardId);
            });

            $(document).on('click', '.pms-task-item', function() {
                const taskId = $(this).data('task-id');
                const modal = $('#pmsEditTaskModal');

                modal.modal('show');

                // Load task details directly for this taskId
                loadTaskDetails(taskId, modal);
            })

            enableTaskDragOver();
        });
    </script>
@endpush
