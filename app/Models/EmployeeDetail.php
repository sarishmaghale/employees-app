<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'employee_id',
        'address',
        'phone',
        'dob',
        'profile_image'
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
