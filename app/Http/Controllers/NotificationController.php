<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        $unreadCount = $user->unreadNotificationsCount();

        return view('notificaciones', compact('notifications', 'unreadCount'));
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Verificar que pertenezca al usuario autenticado
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        // Marcar como leída
        if (!$notification->read) {
            $notification->markAsRead();
        }

        // Redirigir al URL si existe
        if ($notification->url) {
            return redirect($notification->url);
        }

        return redirect()->route('notificaciones');
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();
        return back();
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update(['read' => true]);
        return back();
    }

    public function delete($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();
        return back();
    }
}
