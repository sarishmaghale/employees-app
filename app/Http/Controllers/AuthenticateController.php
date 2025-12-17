<?php

namespace App\Http\Controllers;

use App\Repositories\EmailRepository;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticateController extends Controller
{
    public function __construct(
        protected EmployeeRepository $repo,
        protected EmailRepository $emailService
    ) {}

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
        if (Auth::validate($credentials)) {
            $otpCode = $this->emailService->createOtpForLogIn($request->email);
            $this->emailService->sendOtpMail($request->email, $otpCode);
            // $request->session()->regenerate();
            // $loggedIn = Auth::user();
            // $user = $this->repo->getById($loggedIn->id);
            // Session::put('username', $user->username);
            // Session::put('profile_image', $user->detail->profile_image);
            // Session::put('role', $user->role);
            return response()->json([
                'success' => true,
                'message' => 'Valid credentials',
                'data' => $request->email
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ]);
    }

    public function verifyLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);
        $isValid = $this->emailService->verifyOtp($request->email, $request->otp);
        if ($isValid) {
            $user = $this->repo->getByEmail($request->email);
            Auth::login($user);
            return response()->json([
                'success' => true,
                'message' => 'Login successful'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Invalid OTP'
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
