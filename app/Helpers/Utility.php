<?php

use App\Models\TaskCategory;
use App\Models\TaskCategoryLink;
use App\Models\TaskSubCategory;

if (!function_exists('getTaskCategories')) {
    function getTaskCategories()
    {
        return TaskCategory::all();
    }
}

if (!function_exists('getTaskSubCategories')) {
    function getTaskSubCategories(int $id)
    {
        $category = TaskCategory::find($id);
        return $category->subCategories;
    }
}
