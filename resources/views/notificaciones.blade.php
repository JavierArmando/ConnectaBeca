@extends('layouts.master')

@section('content')

<style>
    body {
        background: #f5f5f5;
        padding-bottom: 90px;
    }

    .page-header {
        background: linear-gradient(135deg, #1976D2 0%, #0d47a1 100%);
        color: white;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }

    .page-header h1 {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #ddd;
    }

    .empty-state-text {
        font-size: 1.1rem;
        color: #666;
    }

    .empty-state-subtext {
        font-size: 0.9rem;
        color: #999;
        margin-top: 10px;
    }

    .notification-item {
        background: white;
        border-left: 4px solid #ccc;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .notification-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .notification-item.unread {
        border-left-color: #1976D2;
        background: #f0f5ff;
    }

    .notification-item.unread .badge {
        display: inline-block;
    }

    .notification-badge {
        display: inline-block;
        background: #1976D2;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        line-height: 20px;
        text-align: center;
        font-size: 0.75rem;
        margin-left: 5px;
    }

    .notification-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        cursor: pointer;
    }

    .notification-message {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 8px;
    }

    .notification-time {
        color: #999;
        font-size: 0.85rem;
    }

    .notification-actions {
        text-align: right;
        padding-top: 10px;
        border-top: 1px solid #eee;
        margin-top: 10px;
    }

    .notification-actions form {
        display: inline-block;
        margin-left: 5px;
    }

    .btn-mark-read {
        background: none;
        border: none;
        color: #1976D2;
        cursor: pointer;
        font-size: 0.85rem;
        padding: 0;
        text-decoration: none;
    }

    .btn-mark-read:hover {
        text-decoration: underline;
    }

    .btn-delete {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
        font-size: 0.85rem;
        padding: 0;
        text-decoration: none;
    }

    .btn-delete:hover {
        text-decoration: underline;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-bell me-2"></i>Notificaciones</h1>
</div>

<div class="container" style="max-width: 700px;">
    @if($notifications->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-bell"></i>
            </div>
            <div class="empty-state-text">No tienes notificaciones</div>
            <div class="empty-state-subtext">Aquí aparecerán tus notificaciones más recientes</div>
        </div>
    @else
        @if($unreadCount > 0)
            <div style="text-align: right; margin-bottom: 15px;">
                <form action="{{ route('notificaciones.markAllAsRead') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-check-circle"></i> Marcar todas como leídas ({{ $unreadCount }})
                    </button>
                </form>
            </div>
        @endif

        @foreach($notifications as $notification)
            <div class="notification-item {{ !$notification->read ? 'unread' : '' }}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="flex-grow: 1; cursor: pointer;" onclick="{{ $notification->url ? 'window.location.href = \'' . $notification->url . '\'' : 'return false;' }}">
                        <div class="notification-title">
                            {{ $notification->title }}
                            @if(!$notification->read)
                                <span class="notification-badge">
                                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                </span>
                            @endif
                        </div>
                        <div class="notification-message">{{ $notification->message }}</div>
                        <div class="notification-time">
                            <i class="fas fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>

                <div class="notification-actions">
                    @if(!$notification->read)
                        <form action="{{ route('notificaciones.markAsRead', $notification->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-mark-read">
                                <i class="fas fa-check"></i> Marcar como leída
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('notificaciones.delete', $notification->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>

@endsection
