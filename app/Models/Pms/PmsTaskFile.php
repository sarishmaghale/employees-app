<?php

namespace App\Models\Pms;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class PmsTaskFile extends Model
{
    protected $table = 'pms_task_files';

    protected $fillable = [
        'task_id',
        'employee_id',
        'file_path',
        'file_name'
    ];

    public function task()
    {
        return $this->belongsTo(PmsTask::class, 'task_id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }
}
