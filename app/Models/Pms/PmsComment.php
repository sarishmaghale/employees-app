<?php

namespace App\Models\Pms;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class PmsComment extends Model
{
    protected $table = 'pms_card_comments';
    protected $fillable = [
        'comment',
        'employee_id',
        'task_id',
        'comment_type'
    ];

    public function task()
    {
        return $this->belongsTo(PmsTask::class, 'task_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'id');
    }
}
