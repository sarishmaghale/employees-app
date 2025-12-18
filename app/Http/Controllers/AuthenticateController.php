<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
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
        $requestedEmail = $request->email;
        if (Auth::validate($credentials)) {
            $getUser = $this->repo->getByEmail($requestedEmail);
            if ($getUser->role !== 'admin') {
                $otpCode = $this->emailService->createOtpForLogIn($requestedEmail);
                if ($otpCode) {
                    $this->emailService->sendOtpMail($requestedEmail, $otpCode);
                    return JsonResponse::success(message: 'Valid credentials', data: $requestedEmail);
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
        $email = $request->input('email');
        $emailExists = $this->repo->getByEmail($email);
        if ($emailExists) {
            $mailSent = $this->emailService->sendResetLinkMail($email);
            if ($mailSent) return JsonResponse::success(message: 'Your password reset link sent to your email address successfully!');
            else return JsonResponse::error(message: 'Failed to send mail');
        } else {
            return JsonResponse::error(message: 'Invalid email');
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
            'password' => 'required'
        ]);
        $valid = $this->emailService->verifyResetLink($request->email, $request->token);
        if ($valid) {
            $user = $this->repo->getByEmail($request->email);
            $updatedData = [
                'username' => $user->username,
                'password' => $request->password,
            ];
            $isUpdated = $this->repo->updateProfile($user, $updatedData);
            if ($isUpdated) return redirect('login')->with('success', 'Password Reset successfull');
            else return redirect('reset-password')->with('error', 'Password reset failed');
        } else return redirect('reset-password')->with('error', 'Invalid credentials');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return view('login');
    }
}
