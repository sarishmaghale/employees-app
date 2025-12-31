<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmsChecklist extends Model
{
    protected $fillable = [
        'task_id',
        'title'
    ];
    public function task()
    {
        return $this->belongsTo(PmsTask::class, 'task_id');
    }
    public function items()
    {
        return $this->hasMany(PmsChecklistItem::class, 'checklist_id');
    }
}
