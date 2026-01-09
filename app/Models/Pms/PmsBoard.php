<?php

namespace App\Models\Pms;

use Illuminate\Database\Eloquent\Model;

class PmsBoard extends Model
{
    protected $table = 'pms_boards';
    protected $fillable = [
        'board_name',
        'created_by',
        'image'
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'created_by', 'id');
    }

    public function cards()
    {
        return $this->hasMany(PmsCard::class, 'board_id', 'id');
    }

    public function members()
    {
        return $this->belongsToMany(
            \App\Models\Employee::class,
            'pms_board_members',
            'board_id',
            'employee_id'
        );
    }
}
