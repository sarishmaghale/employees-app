<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title',
        'start',
        'end',
        'color',
        'employee_id',
        'category_id',
        'badge'
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function taskCategory()
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }
}
