<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmsComment extends Model
{
    protected $table = 'pms_card_comments';
    protected $fillable = [
        'comment',
        'employee_id',
        'task_id'
    ];

    public function task()
    {
        return $this->belongsTo(PmsTask::class, 'task_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
