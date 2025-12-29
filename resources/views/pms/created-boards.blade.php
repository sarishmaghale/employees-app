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
                <button class="kb-add-card-global">+ Add Card</button>
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
                    <span class="task-count">0 Tasks</span>
                </div>
                <div class="kb-column-body">
                    <button class="kb-add-task-btn initiatePmsAddTaskBtn" data-card-id="{{ $card->id }}">
                        + Add Task
                    </button>
                </div>
            </div>
        @empty
            No card yet
        @endforelse
    </div>
    @include('pms.partial-add-task')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('.kb-column').each(function() {
                const cardId = $(this).data('card-id');
                const cardBody = $(this).find('.kb-column-body');
                const taskCount = $(this).find('.task-count');

                $.ajax({
                    url: `/pms-card-tasks/${cardId}`,
                    method: "GET",
                    success: function(tasks) {
                        cardBody.find('.kb-card').remove();
                        if (tasks.length === 0) {
                            taskCount.text('0 Tasks');
                            return;
                        }
                        tasks.forEach(task => {
                            const taskHtml = `
                        <div class="kb-card">
                            <h3>${task.title}</h3>
                            <div class="kb-card-meta">
                                <span class="kb-tag"></span>
                                <span class="kb-date">Due: ${task.end_date || 'N/A'}</span>
                            </div>
                        </div>
                    `;
                            cardBody.prepend(taskHtml);
                        });
                        taskCount.text(`${tasks.length} Task${tasks.length > 1 ? 's' : ''}`);
                    },
                    error: function() {
                        cardBody.html('<p>Failed to load tasks.</p>');
                    }
                })
            });

            $(document).on('click', '.initiatePmsAddTaskBtn', function() {
                const btn = $(this);
                const cardId = btn.data('card-id');

                // Hide button instead
                btn.hide();

                btn.after(`
        <div class="inline-task-form" style="margin-top:6px;">
        <input type="text" class="form-control inline-task-input" style="margin-bottom: 6px;" />
        <button type="button" class="btn btn-primary modal-submit-btn btn-save-task" data-card-id="${cardId}">Save</button>
        <button type="button" class="btn btn-secondary btn-cancel-task">Cancel</button>
    </div>
    `);
            });


            $(document).on('click', '.btn-cancel-task', function() {
                const form = $(this).closest('.inline-task-form');
                form.prev('.initiatePmsAddTaskBtn').show(); // show original button
                form.remove();
            });

        })
    </script>
@endpush
