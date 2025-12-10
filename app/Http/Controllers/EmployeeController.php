<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Repositories\EmployeeRepository;
use App\Repositories\TaskRepository;
use PHPUnit\TextUI\XmlConfiguration\FailedSchemaDetectionResult;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employeeRepo,
        protected TaskRepository $taskRepo
    ) {}

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
        return view('employees');
    }

    public function employeesList()
    {
        $this->checkAdmin();
        $employees = $this->employeeRepo->getAll();
        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $this->checkAdmin();
        $validatedData = $request->validate([
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|string',
            'username' => 'required|string',
            'role' => 'required|string',
        ]);
        $validatedData['password'] = bcrypt($validatedData['password']);
        $employee = $this->employeeRepo->storeEmployee($validatedData);
        if ($employee === null) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add Employee'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Employee ' . $employee->username . ' added successfully',
        ]);
    }

    public function show(int $employeeId)
    {
        $this->checkAdmin();
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

    public function update(Request $request, $id)
    {
        $this->checkAdmin();
        $employee = $this->employeeRepo->getById($id);
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

    public function modifyProfile(Request $request, int $id)
    {
        $user = $this->employeeRepo->getById($id);
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
            if ($request->hasFile('profile_image')) {
                $imagePath = $request->file('profile_image')->store('pfp', 'public');
                $validatedData['profile_image'] = $imagePath;
            }
            $isUpdated = $this->employeeRepo->updateProfile($user, $validatedData);
            if ($isUpdated !== null) {
                Session::put('username', $isUpdated->username);
                Session::put('profile_image', $isUpdated->detail->profile_image);
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

    public function task(int $id)
    {
        $employee = $this->employeeRepo->getById($id);
        return view('add-task', compact('employee'));
    }
}
