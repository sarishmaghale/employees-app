<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\EmployeeDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeRepository
{
    public function storeEmployee(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $employee = Employee::create([
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'username' => $data['username'],
                'role' => $data['role'],
                'isDeleted' => 0
            ]);
            $employee->detail = EmployeeDetail::create([
                'employee_id' => $employee->id,
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'dob' => $data['dob'] ?? null,
            ]);
            return $employee;
        });
    }

    public function getById(int $employeeId): Employee
    {
        return Employee::with('detail')->find($employeeId);
    }

    public function getByEmail(string $email): Employee
    {
        return Employee::with('detail')->where('email', $email)->first();
    }
    public function getAll(): Collection
    {
        return Employee::with('detail')->where('isDeleted', 0)
            ->get();
    }

    public function updateEmployee(Employee $model, array $employee,): bool
    {
        return DB::transaction(function () use ($model, $employee) {
            $model->update([
                'email' => $employee['email'],
                'role' => $employee['role'],
                'username' => $employee['username'],
            ]);
            if ($model->detail) {
                $model->detail->update([
                    'address' => $employee['address'] ?? null,
                    'phone' => $employee['phone'] ?? null,
                    'dob' => $employee['dob'] ?? null
                ]);
            }
            return true;
        });
    }

    public function updateProfile(Employee $profile, array $personalInfo): Employee
    {
        return DB::transaction(function () use ($profile, $personalInfo) {
            $password = !empty($personalInfo['password'])
                ? bcrypt($personalInfo['password'])
                : $profile->password;
            $profile->update([
                'username' => $personalInfo['username'],
                'password' => $password
            ]);
            if ($profile->detail) {
                $detailData = [
                    'address' => $personalInfo['address'],
                    'phone' => $personalInfo['phone'],
                    'dob' => $personalInfo['dob'],
                ];
                if (!empty($personalInfo['profile_image'])) {
                    $detailData['profile_image'] = $personalInfo['profile_image'];
                }
                $profile->detail->update($detailData);
            }
            return Employee::with('detail')->find($profile->id);
        });
    }

    public function deleteEmployee(Employee $employee): bool
    {
        $employee->update([
            'isDeleted' => 1
        ]);
        return true;
    }
}
