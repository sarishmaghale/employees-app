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
        'created_by'
    ];

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'assigned_to', 'id');
    }

    public function card()
    {
        return $this->belongsTo(PmsCard::class, 'card_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(PmsComment::class, 'task_id');
    }
}
