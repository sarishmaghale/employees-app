<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskSubCategory extends Model
{

    protected $fillable = [
        'sub_category_name',
    ];

    public function mainCategories()
    {
        return $this->belongsToMany(
            TaskCategory::class,
            'task_category_links',
            'sub_category_id',
            'category_id'
        );
    }
}
