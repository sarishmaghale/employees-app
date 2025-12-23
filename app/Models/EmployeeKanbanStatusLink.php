<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeKanbanStatusLink extends Model
{
    protected $fillable = [
        'employee_id',
        'category_id',
        'status_id',
        'position',
        'name'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function taskCategory()
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }
    public function status()
    {
        return $this->belongsTo(KanbanStatus::class, 'status_id');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'status_link_id');
    }
}
