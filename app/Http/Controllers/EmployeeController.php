<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeDetail;
use Illuminate\Support\Facades\Auth;
use App\Repositories\EmployeeRepository;
use PHPUnit\TextUI\XmlConfiguration\FailedSchemaDetectionResult;

class EmployeeController extends Controller
{
    public function __construct(protected EmployeeRepository $employeeRepo) {}

    private function checkAdmin()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }

    public function index()
    {
        $this->checkAdmin();
        $employees = $this->employeeRepo->getAll();
        return response()->json($employees);
    }

    public function create(Request $request)
    {
        $this->checkAdmin();
        $validatedData = $request->validate([
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|string|min:6',
            'username' => 'required|string',
            'role' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable',
            'dob' => 'nullable',
        ]);
        $validatedData['password'] = bcrypt($validatedData['password']);
        $employee = $this->employeeRepo->storeEmployee($validatedData);
        if ($employee !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add Employee'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Employee ' + $employee->username + ' added successfully',
        ]);
    }

    public function show(int $employeeId)
    {
        $this->checkAdmin();
        $employee = $this->employeeRepo->getById($employeeId);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Employee ID'
            ]);
        }
        return response()->json($employee);
    }

    public function profileView()
    {
        $user = Auth::user();
        $userId = $user->id;
        $profileData = $this->employeeRepo->getById($userId);
        return view('profile', compact('profileData'));
    }

    public function update(Request $request, int $employeeId)
    {
        $this->checkAdmin();
        $employee = $this->employeeRepo->getById($employeeId);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Employee Id'
            ]);
        } else {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'username' => 'required|string',
                'role' => 'required|string',
                'address' => 'nullable|string',
                'phone' => 'nullable',
                'dob' => 'nullable',
            ]);
            $isUpdated = $this->employeeRepo->updateEmployee($employee, $validatedData);
            if ($isUpdated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data updated successfully'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to update'
            ]);
        }
    }

    public function modifyProfile(Request $request, int $userId)
    {
        $user = $this->employeeRepo->getById($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user'
            ]);
        } else {
            $validatedData = $request->validate([
                'username' => 'required|string',
                'address' => 'nullable|string',
                'phone' => 'nullable',
                'dob' => 'nullable',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg'
            ]);
            if ($$request->hasFile('profile_image')) {
                $imagePath = $request->file('profile_image')->store('pfp', 'public');
                $validatedData['profile_image'] = $imagePath;
            }
            $isUpdated = $this->employeeRepo->updateProfile($user, $validatedData);
            if ($isUpdated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update profile'
                ]);
            }
        }
    }

    public function delete(int $employeeId)
    {
        $this->checkAdmin();
        $employee = $this->employeeRepo->getById($employeeId);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Employee Id'
            ]);
        }
        $deleted = $this->employeeRepo->deleteEmployee($employee);
        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete Employee'
        ]);
    }
}
