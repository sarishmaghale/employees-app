<?php

namespace App\Http\Controllers;

use App\Helpers\JsonReponse;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Repositories\EmployeeRepository;
use App\Repositories\TaskRepository;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employeeRepo,
        protected TaskRepository $taskRepo
    ) {}

    public function index()
    {
        return view('employees');
    }

    public function employeesList()
    {
        $employees = $this->employeeRepo->getAll();
        return response()->json($employees);
    }

    public function store(StoreEmployeeRequest $request)
    {
        $validatedData = $request->validated();
        $employee = $this->employeeRepo->storeEmployee($validatedData);
        if ($employee === null) {
            return JsonReponse::error(message: 'Failed to add Employee');
        }
        return JsonReponse::success(message: "Employee $employee->username added successfully'");
    }

    public function show(int $employeeId)
    {
        $employee = $this->employeeRepo->getById($employeeId);
        if (!$employee) {
            return view('employees');
        }
        return view('edit-employee', compact('employee'));
    }

    public function profileView()
    {
        $user = Auth::user();
        $userId = $user->id;
        $profileData = $this->employeeRepo->getById($userId);
        return view('edit-profile', compact('profileData'));
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        $employee = $this->employeeRepo->getById($id);
        if (!$employee) {
            return JsonReponse::error(message: 'Invalid Employee Id');
        } else {
            $validatedData = $request->validated();
            $isUpdated = $this->employeeRepo->updateEmployee($employee, $validatedData);
            if ($isUpdated) {
                return JsonReponse::success(message: 'Data updated successfully');
            }
            return JsonReponse::error(message: 'Failed to update');
        }
    }

    public function modifyProfile(UpdateProfileRequest $request, int $id)
    {
        $user = $this->employeeRepo->getById($id);
        if (!$user) {
            return JsonReponse::error(message: 'Invalid user');
        } else {
            $validatedData = $request->validated();
            if ($request->hasFile('profile_image')) {
                $imagePath = $request->file('profile_image')->store('pfp', 'public');
                $validatedData['profile_image'] = $imagePath;
            }
            $isUpdated = $this->employeeRepo->updateProfile($user, $validatedData);
            if ($isUpdated !== null) {
                Session::put('username', $isUpdated->username);
                Session::put('profile_image', $isUpdated->detail->profile_image ?? null);
                return JsonReponse::success(message: 'Profile updated successfully');
            } else {
                return JsonReponse::error(message: 'Failed to update profile');
            }
        }
    }

    public function delete(int $employeeId)
    {
        $employee = $this->employeeRepo->getById($employeeId);
        if (!$employee) {
            return JsonReponse::error(message: 'Invalid Employee Id');
        }
        $deleted = $this->employeeRepo->deleteEmployee($employee);
        if ($deleted) {
            return JsonReponse::success(message: 'Employee deleted successfully');
        }
        return JsonReponse::error(message: 'Failed to delete employee');
    }

    public function task(int $id)
    {
        $employee = $this->employeeRepo->getById($id);
        return view('add-task', compact('employee'));
    }
}
