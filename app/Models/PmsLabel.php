<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmsLabel extends Model
{

    protected $fillable = [
        'title',
        'color',
    ];

    public function tasks()
    {
        return $this->belongsToMany(
            PmsTask::class,
            'pms_task_labels',
            'label_id',
            'task_id'
        );
    }
}
