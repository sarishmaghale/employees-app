<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskCategory extends Model
{
    protected $fillable = [
        'category_name',
        'color',
    ];
    public function tasks()
    {
        return $this->hasMany(Task::class, 'category_id');
    }

    public function subCategories()
    {
        return $this->belongsToMany(
            TaskSubCategory::class,
            'task_category_links',
            'category_id',
            'sub_category_id'
        );
    }
}
