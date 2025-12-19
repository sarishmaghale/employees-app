<?php

namespace App\Http\Controllers;

use App\Mail\LoginOtpMail;
use Illuminate\Http\Request;
use App\Helpers\JsonResponse;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Repositories\EmailRepository;
use Illuminate\Support\Facades\Session;
use App\Repositories\EmployeeRepository;

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

    public function newPassword(Request $request)
    {
        $email = $request->query('email');
        return view('create-new-password', compact('email'));
    }

    public function saveNewPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:employees,email',
            'password' => 'required|confirmed',
        ]);
        $user = $this->repo->getByEmail($request->email);
        $updatedData = [
            'username' => $user->username,
            'password' => $request->password
        ];
        $this->repo->updateProfile($user, $updatedData);
        $this->repo->activateAccount($user);
        return redirect()->route('login')->with('success', 'Password set successfully');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $requestedEmail = $request->email;
        if (Auth::validate($credentials)) {
            $getUser = $this->repo->getByEmail($requestedEmail);
            if ($getUser->role !== 'admin') {
                if (!$getUser->isActive) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('password.new', ['email' => $requestedEmail])
                    ]);
                }
                $otpCode = $this->emailService->createOtpForLogIn($requestedEmail);
                if ($otpCode) {
                    try {
                        Mail::to($requestedEmail)->send(new LoginOtpMail($otpCode));
                        return JsonResponse::success(message: 'Valid credentials', data: $requestedEmail);
                    } catch (\Throwable $e) {
                        return JsonResponse::error(message: 'Invalid OTP');
                    }
                } else {
                    return JsonResponse::error(message: 'Failed to generate OTP');
                }
            } else {
                Auth::login($getUser);
                Session::put('username', $getUser->username);
                Session::put('profile_image', $getUser->detail->profile_image);
                Session::put('role', $getUser->role);
                return response()->json([
                    'success' => true,
                    'role' => $getUser->role,
                ]);
            }

            // $request->session()->regenerate();
            // $loggedIn = Auth::user();
            // $user = $this->repo->getById($loggedIn->id);
            // Session::put('username', $user->username);
            // Session::put('profile_image', $user->detail->profile_image);
            // Session::put('role', $user->role);

        }
        return JsonResponse::error(message: 'Invalid credentials');
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
            Session::put('username', $user->username);
            Session::put('profile_image', $user->detail->profile_image);
            Session::put('role', $user->role);
            return JsonResponse::success(message: 'Login successfull');
        }
        return JsonResponse::error(message: 'Invalid OTP');
    }

    public function initiatePasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:employees,email'
        ]);
        $email = $request->input('email');

        $url = $this->emailService->generateResetLink($email);
        try {
            Mail::to($email)->send(new ResetPasswordMail($url));
            return JsonResponse::success(message: 'Your password reset link sent to your email address successfully!');
        } catch (\Throwable $e) {
            return JsonResponse::error(message: 'Failed to send mail');
        }
    }

    public function showResetForm(Request $request)
    {
        if (!$request->token || !$request->email) abort(404, 'Invalid link');
        return view('reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed'
        ]);
        $valid = $this->emailService->verifyResetLink($request->email, $request->token);
        if ($valid) {
            $user = $this->repo->getByEmail($request->email);
            $updatedData = [
                'username' => $user->username,
                'password' => $request->password,
            ];
            $isUpdated = $this->repo->updateProfile($user, $updatedData);
            if ($isUpdated) return JsonResponse::success(message: 'Password reset successfull');
            else return JsonResponse::error(message: 'Password reset failed');
        } else return JsonResponse::error(message: 'Invalid or expired link');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}
