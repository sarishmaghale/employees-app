<?php

namespace App\Models;

use App\Models\Pms\PmsTask;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;
    protected $fillable = [
        'email',
        'password',
        'username',
        'role',
        'isDeleted',
        'isActive',
    ];
    public function detail()
    {
        return $this->hasOne(EmployeeDetail::class, 'employee_id');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'employee_id');
    }
    public function kanbanLinks()
    {
        return $this->hasMany(EmployeeKanbanStatusLink::class);
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(
            PmsTask::class,
            'pms_task_assignments',
            'employee_id',
            'task_id'
        );
    }
}
