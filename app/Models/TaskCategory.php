<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskCategory extends Model
{
    protected $fillable = [
        'category_name'
    ];
    public function tasks()
    {
        return $this->hasMany(Task::class, 'category_id');
    }
}
