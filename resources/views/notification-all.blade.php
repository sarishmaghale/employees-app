@extends('layout')

@section('content')
    <div class="container-fluid notifications-page">
        <div class="page-header">
            <div class="page-title-section">
                <h2>Notifications</h2>
                <p class="page-subtitle">All your notifications</p>
            </div>
            <div>
                <a href="#" class="btn btn-primary">Mark All as Read</a>
            </div>
        </div>

        <div class="notifications-page-container">
            @if (auth()->user()->notifications->count() > 0)
                <div class="notification-list">
                    @foreach (auth()->user()->notifications as $notification)
                        <a href="#" class="notification-item {{ $notification->read_at ? '' : 'unread' }}"
                            data-notification-id="{{ $notification->id }}"
                            data-url="{{ route('notification.read', $notification->id) }}">
                            <div class="notification-icon bg-primary">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">
                                    {{ $notification->data['message'] ?? 'New notification' }}
                                </p>
                                <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>No notifications yet</p>
                </div>
            @endif
        </div>
    </div>

@endsection
