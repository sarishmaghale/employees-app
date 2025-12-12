<?php

use App\Models\TaskCategory;

if (!function_exists('getTaskCategories')) {
    function getTaskCategories()
    {
        return TaskCategory::all();
    }
}
