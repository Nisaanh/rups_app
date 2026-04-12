<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        // Opsional: Redirect ke halaman terkait berdasarkan data di notifikasi
        $data = json_decode($notification->data, true);
        
        if ($notification->type === 'approval') {
            return redirect()->route('approval.index');
        } elseif ($notification->type === 'arahan') {
            return redirect()->route('arahan.show', $data['arahan_id']);
        }

        return back()->with('success', 'Notifikasi ditandai telah dibaca.');
    }
}