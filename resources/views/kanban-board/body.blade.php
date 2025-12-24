@foreach ($columns as $col)
    <div class="kb-column">
        <div class="kb-column-header">
            <div class="kb-column-header-top">
                <h2>{{ $col->status->name }}</h2>
                <button class="kb-add-task-btn" data-status-id="{{ $col->id }}"
                    data-category-id="{{ $activeCategoryId }}">
                    + Add Task
                </button>
            </div>
            <span>{{ $col->tasks ? count($col->tasks) : 0 }} Tasks</span>
        </div>
        <div class="kb-column-body">
            @if ($col->tasks && count($col->tasks) > 0)
                @foreach ($col->tasks as $task)
                    <div class="kb-card">
                        <h3>{{ $task->title }}</h3>
                        <div class="kb-card-meta">
                            <span class="kb-tag">{{ $task->badge }}</span>
                            <span class="kb-date">Due: {{ $task->end }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endforeach
