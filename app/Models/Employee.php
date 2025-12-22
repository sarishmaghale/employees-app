<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;
    protected $fillable = [
        'email',
        'password',
        'username',
        'role',
        'isDeleted',
        'isActive',
    ];
    public function detail()
    {
        return $this->hasOne(EmployeeDetail::class, 'employee_id');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'employee_id');
    }
}
