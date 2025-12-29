<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmsBoard extends Model
{
    protected $table = 'pms_boards';
    protected $fillable = ['board_name', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by', 'id');
    }

    public function cards()
    {
        return $this->hasMany(PmsCard::class, 'board_id', 'id');
    }

    public function members()
    {
        return $this->belongsToMany(
            Employee::class,
            'pms_board_members',
            'board_id',
            'employee_id'
        );
    }
}
