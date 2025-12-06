<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    public $timestamps = false;
    protected $fillable = [
        'email',
        'password',
        'username',
        'role',
        'isDeleted'
    ];
    public function detail()
    {
        return $this->hasOne(EmployeeDetail::class, 'employee_id');
    }
}
