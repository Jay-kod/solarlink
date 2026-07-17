<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\ServiceNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Get conversations.
     * Admins see ALL conversations. Regular users see only their participated group chats.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            // Admins audit all active & concluded chats
            $conversations = Conversation::with(['users', 'messages'])
                ->get()
                ->map(function ($conv) {
                    return [
                        'id' => $conv->id,
                        'title' => $conv->title ?: 'Group Chat #' . $conv->id,
                        'status' => $conv->status,
                        'participants' => $conv->users->map(fn($u) => [
                            'name' => $u->name,
                            'role' => $u->role,
                            'avatar' => $u->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=0ea5e9&color=fff'
                        ]),
                        'lastMessage' => $conv->messages()->latest()->first()?->text ?? 'No messages yet.',
                        'time' => $conv->messages()->latest()->first()?->created_at->diffForHumans() ?? $conv->updated_at->diffForHumans(),
                    ];
                });

            return Inertia::render('Admin/ChatAudit/Index', [
                'conversations' => $conversations
            ]);
        }

        // Regular users (Customer, Tech, Vendor) see their shared chats
        $conversations = $user->conversations()
            ->with(['users.technicianProfile', 'messages'])
            ->get()
            ->map(function ($conv) use ($user) {
                $lastSentByUserAt = $conv->messages
                    ->where('sender_id', $user->id)
                    ->max('created_at');

                $incomingMessages = $conv->messages
                    ->where('sender_id', '!=', $user->id);

                $unreadCount = $lastSentByUserAt
                    ? $incomingMessages->where('created_at', '>', $lastSentByUserAt)->count()
                    : $incomingMessages->count();

                $isOnline = $conv->users->contains(function ($participant) use ($user) {
                    if ($participant->id === $user->id) {
                        return false;
                    }

                    return optional($participant->technicianProfile)->status === 'online';
                });
                
                return [
                    'id' => $conv->id,
                    'title' => $conv->title ?: 'Installation Group Chat',
                    'status' => $conv->status,
                    'created_by' => $conv->created_by,
                    'participants' => $conv->users->map(fn($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'role' => $u->role,
                        'avatar' => $u->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=0ea5e9&color=fff'
                    ])->values()->toArray(),
                    'lastMessage' => $conv->messages()->latest()->first()?->text ?? '',
                    'time' => $conv->messages()->latest()->first()?->created_at->diffForHumans() ?? $conv->updated_at->diffForHumans(),
                    'unread' => $unreadCount,
                    'online' => $isOnline,
                ];
            });

        return Inertia::render('Customer/Chat/Index', [
            'initialConversations' => $conversations
        ]);
    }

    /**
     * Search users for creating a new conversation.
     */
    public function searchUsers(Request $request)
    {
        $query = $request->input('q');
        
        if (!$query) {
            return response()->json([]);
        }

        $users = \App\Models\User::where('id', '!=', auth()->id())
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('role', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'role', 'avatar', 'email')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff',
                ];
            });

        return response()->json($users);
    }

    /**
     * Create a new conversation and attach participants.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'title' => 'nullable|string|max:255'
        ]);

        $participantIds = array_unique(array_merge($request->user_ids, [auth()->id()]));

        // Optionally, check if a direct conversation already exists between these exact users
        // But for simplicity, we can just create a new one, or if they want group chats, it's fine to create.
        
        $title = $request->title ?: 'Group Chat';
        
        $conversation = Conversation::create([
            'title' => $title,
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        $conversation->users()->attach($participantIds);

        // Notify added participants (excluding creator)
        $notifyUsers = \App\Models\User::whereIn('id', array_diff($participantIds, [auth()->id()]))->get();
        foreach ($notifyUsers as $u) {
            $prefix = $u->role === 'customer' ? '/user' : '/' . $u->role;
            ServiceNotification::create([
                'user_id' => $u->id,
                'title' => 'New Group Chat',
                'body' => auth()->user()->name . ' started a group chat: ' . $title,
                'type' => 'info',
                'link' => $prefix . '/chat'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'status' => $conversation->status,
                'created_by' => $conversation->created_by,
                'participants' => $conversation->users->map(fn($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'role' => $u->role,
                    'avatar' => $u->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=0ea5e9&color=fff'
                ])->values()->toArray(),
                'lastMessage' => '',
                'time' => 'Just now',
                'unread' => 0,
                'online' => false,
            ]
        ]);
    }

    /**
     * Retrieve Messages for Active Conversation.
     */
    public function getMessages(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender' => $msg->sender_id === auth()->id() ? 'me' : 'them',
                    'sender_name' => $msg->sender->name,
                    'sender_role' => $msg->sender->role,
                    'sender_avatar' => $msg->sender->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->sender->name) . '&background=0ea5e9&color=fff',
                    'text' => $msg->text,
                    'time' => $msg->created_at->format('g:i A'),
                    'type' => $msg->type,
                    'fileName' => $msg->file_name,
                    'fileSize' => $msg->file_size,
                    'imageUrl' => $msg->file_path ? asset('storage/' . $msg->file_path) : null,
                ];
            });

        return response()->json($messages);
    }

    /**
     * Post a New Message to Conversation.
     */
    public function storeMessage(Request $request, Conversation $conversation)
    {
        $this->authorize('sendMessage', $conversation);

        $request->validate([
            'text' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
            'type' => 'required|in:text,image,file'
        ]);

        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filePath = $file->store('chat_attachments', 'public');
            $fileName = $file->getClientOriginalName();
            $fileSize = $this->formatBytes($file->getSize());
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'text' => $request->text,
            'type' => $request->type,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
        ]);

        // Notify other participants
        $otherUsers = $conversation->users()->where('users.id', '!=', auth()->id())->get();
        foreach ($otherUsers as $u) {
            $prefix = $u->role === 'customer' ? '/user' : '/' . $u->role;
            ServiceNotification::create([
                'user_id' => $u->id,
                'title' => 'New Message from ' . auth()->user()->name,
                'body' => $request->type === 'text' ? str($request->text)->limit(50) : 'Sent an attachment',
                'type' => 'info',
                'link' => $prefix . '/chat'
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Message stored successfully'
            ]);
        }

        return back();
    }

    /**
     * Soft finish / Conclude conversation (keep history, but make read-only).
     */
    public function conclude(Conversation $conversation)
    {
        $this->authorize('manageLifecycle', $conversation);

        $conversation->update(['status' => 'concluded']);

        return redirect()->back()->with('success', 'Conversation marked as concluded.');
    }

    /**
     * Add new members to an existing conversation.
     */
    public function addMembers(Request $request, Conversation $conversation)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Get IDs already in conversation
        $existingIds = $conversation->users()->pluck('users.id')->toArray();
        $newIds = array_diff($request->user_ids, $existingIds);

        if (empty($newIds)) {
            return response()->json([
                'status' => 'info',
                'message' => 'All selected users are already in this conversation.'
            ]);
        }

        $conversation->users()->attach($newIds);

        // Post a system message about the new members
        $newUsers = \App\Models\User::whereIn('id', $newIds)->get();
        $names = $newUsers->pluck('name')->join(', ');
        
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'text' => auth()->user()->name . ' added ' . $names . ' to the chat.',
            'type' => 'system',
        ]);

        // Notify new members
        foreach ($newUsers as $u) {
            $prefix = $u->role === 'customer' ? '/user' : '/' . $u->role;
            ServiceNotification::create([
                'user_id' => $u->id,
                'title' => 'Added to Chat',
                'body' => auth()->user()->name . ' added you to a chat.',
                'type' => 'info',
                'link' => $prefix . '/chat'
            ]);
        }

        // Return updated participant list
        $conversation->load('users');
        $participants = $conversation->users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'role' => $u->role,
            'avatar' => $u->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=0ea5e9&color=fff'
        ])->values()->toArray();

        return response()->json([
            'status' => 'success',
            'message' => $names . ' added to the chat.',
            'participants' => $participants,
        ]);
    }

    /**
     * Remove a member from an existing conversation.
     */
    public function removeMember(Request $request, Conversation $conversation, \App\Models\User $user)
    {
        // For simplicity based on recent rules: anyone can add or remove.
        // We'll just ensure the user is actually in the conversation.
        if (!$conversation->users()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not in this conversation.'
            ], 400);
        }

        $conversation->users()->detach($user->id);

        // Post a system message about the removal
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'text' => auth()->user()->name . ' removed ' . $user->name . ' from the chat.',
            'type' => 'system',
        ]);

        // Notify removed member
        $prefix = $user->role === 'customer' ? '/user' : '/' . $user->role;
        ServiceNotification::create([
            'user_id' => $user->id,
            'title' => 'Removed from Chat',
            'body' => auth()->user()->name . ' removed you from the chat.',
            'type' => 'warning',
            'link' => $prefix . '/chat'
        ]);

        // Return updated participant list
        $conversation->load('users');
        $participants = $conversation->users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'role' => $u->role,
            'avatar' => $u->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=0ea5e9&color=fff'
        ])->values()->toArray();

        return response()->json([
            'status' => 'success',
            'message' => $user->name . ' removed from the chat.',
            'participants' => $participants,
        ]);
    }

    /**
     * Delete conversation group.
     */
    public function destroy(Conversation $conversation)
    {
        $this->authorize('manageLifecycle', $conversation);

        $conversation->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Conversation group deleted.'
            ]);
        }

        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.chat.index')->with('success', 'Conversation group deleted.');
        } elseif ($user->role === 'technician') {
            return redirect()->route('technician.chat.index')->with('success', 'Conversation group deleted.');
        } elseif ($user->role === 'vendor') {
            return redirect()->route('vendor.chat.index')->with('success', 'Conversation group deleted.');
        }

        return redirect()->route('chat.index')->with('success', 'Conversation group deleted.');
    }

    /**
     * Format file size.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
