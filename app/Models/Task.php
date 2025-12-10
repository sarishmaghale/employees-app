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
        'isImportant',
        'color',
        'employee_id'
    ];
    public function employee()
    {
        return $this->belongsTo(EMployee::class, 'employee_id');
    }
}
