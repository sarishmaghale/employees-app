<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanbanStatus extends Model
{
    protected $fillable = [
        'name'
    ];
    public function kanbanLinks()
    {
        return $this->hasMany(EmployeeKanbanStatusLink::class, 'status_id');
    }
}
