<?php

namespace App\Http\Controllers;

use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticateController extends Controller
{
    public function __construct(protected EmployeeRepository $repo) {}

    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $loggedIn = Auth::user();
            $user = $this->repo->getById($loggedIn->id);
            Session::put('username', $user->username);
            Session::put('profile_image', $user->detail->profile_image);
            Session::put('role', $user->role);
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return view('login');
    }
}
