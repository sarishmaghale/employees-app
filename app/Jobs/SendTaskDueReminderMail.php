<?php

namespace App\Jobs;

use App\Models\PmsTask;
use App\Models\Employee;
use App\Mail\TaskDueReminderMail;
use App\Repositories\PmsRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\TaskDueReminderNotification;

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

            $task->reminder_for_date !== $this->reminderForDate
        ) {
            return;
        }
        if ($task->assignedEmployees) {
            foreach ($task->assignedEmployees as $employee) {
                $employee->notify(new TaskDueReminderNotification($task));
                Mail::to($employee->email)
                    ->queue(new TaskDueReminderMail($task));
            };
        }
    }
}
