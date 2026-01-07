<?php

namespace App\Jobs;

use App\Models\PmsTask;
use App\Mail\TaskDueReminderMail;
use App\Repositories\PmsRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTaskDueReminderMail implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $taskId,
        protected string $reminderForDate
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $task = PmsTask::with('assignedEmployees:id,email')
            ->find($this->taskId);

        if (
            $task->reminder_sent_at ||
            $task->reminder_for_date !== $this->reminderForDate
        ) {
            return;
        }
        if ($task->assignedEmployees) {
            foreach ($task->assignedEmployees as $employee) {
                Mail::to($employee->email)
                    ->send(new TaskDueReminderMail($task));
            };
        }
        $task->update([
            'reminder_sent_at' => now(),
        ]);
    }
}
