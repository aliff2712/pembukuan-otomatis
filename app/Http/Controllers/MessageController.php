<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    // Tampilkan inbox
    public function index()
    {
        $authId = Auth::id();

        $users = User::where('id', '!=', $authId)
            ->withCount([
                'sentMessages as unread_count' => function (Builder $q) use ($authId) {
                    $q->where('receiver_id', $authId)
                      ->where('is_read', false);
                }
            ])
            ->orderBy('name')
            ->get();

        return view('messages.index', compact('users'));
    }

    // Tampilkan chat dengan user tertentu
    public function show($userId)
    {
        $authId = Auth::id();

        $receiver = User::findOrFail($userId);

        Message::where('sender_id', $userId)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $messages = Message::with(['sender:id,name,email', 'receiver:id,name,email'])
            ->where(function (Builder $query) use ($userId, $authId) {
                $query->where(function (Builder $q) use ($userId, $authId) {
                    $q->where('sender_id', $authId)
                      ->where('receiver_id', $userId);
                })->orWhere(function (Builder $q) use ($userId, $authId) {
                    $q->where('sender_id', $userId)
                      ->where('receiver_id', $authId);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $users = User::where('id', '!=', $authId)
            ->withCount([
                'sentMessages as unread_count' => function (Builder $q) use ($authId) {
                    $q->where('receiver_id', $authId)
                      ->where('is_read', false);
                }
            ])
            ->orderBy('name')
            ->get();

        return view('messages.show', compact('receiver', 'messages', 'users'));
    }

    // Kirim pesan
    public function store(Request $request)
    {
        $authId = Auth::id();

        $request->validate([
            'receiver_id' => 'required|exists:users,id|different:' . $authId,
            'message' => 'required|string|max:1000|min:1',
        ], [
            'receiver_id.different' => 'Tidak bisa mengirim pesan ke diri sendiri.',
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.max' => 'Pesan maksimal 1000 karakter.',
        ]);

        $message = Message::create([
            'sender_id' => $authId,
            'receiver_id' => $request->receiver_id,
            'message' => trim($request->message),
        ]);

        $message->load('sender:id,name,email', 'receiver:id,name,email');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->route('messages.show', $request->receiver_id)
            ->with('success', 'Pesan berhasil dikirim');
    }

    // Get unread count
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    // Get new messages
    public function getNewMessages($userId)
    {
        $authId = Auth::id();
        $lastTimestamp = request('last_timestamp', now()->subMinutes(5));

        $messages = Message::with(['sender:id,name,email', 'receiver:id,name,email'])
            ->where(function ($query) use ($userId, $authId) {
                $query->where(function ($q) use ($userId, $authId) {
                    $q->where('sender_id', $authId)
                      ->where('receiver_id', $userId);
                })->orWhere(function ($q) use ($userId, $authId) {
                    $q->where('sender_id', $userId)
                      ->where('receiver_id', $authId);
                });
            })
            ->where('created_at', '>', $lastTimestamp)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->isNotEmpty()) {
            Message::where('sender_id', $userId)
                ->where('receiver_id', $authId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
        }

        return response()->json($messages);
    }

    // Recent messages dropdown
    public function getRecentMessages()
    {
        $messages = Message::with(['sender:id,name,email'])
            ->where('receiver_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        return response()->json($messages);
    }
}
