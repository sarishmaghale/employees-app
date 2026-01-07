<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmsTask extends Model
{
    protected $table = 'pms_tasks';
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'description',
        'position',
        'assigned_to',
        'card_id',
        'created_by',
        'checklist_items',
        'labels',
        'reminder_sent_at',
        'reminder_for_date'
    ];

    public function assignedEmployees()
    {
        return $this->belongsToMany(
            Employee::class,
            'pms_task_assignments',
            'task_id',
            'employee_id'
        );
    }

    public function card()
    {
        return $this->belongsTo(PmsCard::class, 'card_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(PmsComment::class, 'task_id')
            ->orderBy('created_at', 'desc');;
    }

    public function checklists()
    {
        return $this->hasMany(PmsChecklist::class, 'task_id');
    }

    public function checklistItems()
    {
        return $this->hasManyThrough(
            PmsChecklistItem::class,
            PmsChecklist::class,
            'task_id',        // Foreign key on intermediate table
            'checklist_id',   // Foreign key on final table
            'id',             // Local key on tasks
            'id'              // Local key on checklists
        );
    }
    public function labels()
    {
        return $this->belongsToMany(
            PmsLabel::class,
            'pms_task_labels',
            'task_id',
            'label_id'
        );
    }
    public function files()
    {
        return $this->hasMany(PmsTaskFile::class, 'task_id');
    }
}
