@if ($type === 0)
    <p>A new task has been assigned to you:</p>
@else
    <p>Your task has been updated:</p>
@endif

<p><strong>{{ $task->title }}</strong></p>
<p>Deadline: {{ $task->end }}</p>
