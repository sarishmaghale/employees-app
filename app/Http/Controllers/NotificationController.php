<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->unreadNotifications->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();

            return JsonResponse::success(message: 'Notification marked as read');
        }

        return JsonResponse::error(message: 'Notification not found');
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        return JsonResponse::success(message: 'All notification set to read');
    }
    public function index()
    {
        return view('notification-all');
    }
}
