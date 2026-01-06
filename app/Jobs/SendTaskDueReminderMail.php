<?php

namespace App\Jobs;

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
    public function __construct(protected int $taskId) {}

    /**
     * Execute the job.
     */
    public function handle(PmsRepository $repo): void
    {
        $task = $repo->getTaskDetails($this->taskId);
        if ($task->assignedEmployees) {
            foreach ($task->assignedEmployees as $employee) {
                Mail::to($employee->email)
                    ->queue(new TaskDueReminderMail($task));
            };
        }
    }
}
