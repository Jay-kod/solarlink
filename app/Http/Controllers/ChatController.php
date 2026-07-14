<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
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
                            'avatar' => $u->avatar
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
            ->with(['users'])
            ->get()
            ->map(function ($conv) use ($user) {
                // Other participants in this tripartite chat (excluding the current user)
                $peers = $conv->users->where('id', '!=', $user->id);
                
                return [
                    'id' => $conv->id,
                    'title' => $conv->title ?: 'Installation Group Chat',
                    'status' => $conv->status,
                    'participants' => $conv->users->map(fn($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'role' => $u->role,
                        'avatar' => $u->avatar
                    ])->values()->toArray(),
                    'lastMessage' => $conv->messages()->latest()->first()?->text ?? '',
                    'time' => $conv->messages()->latest()->first()?->created_at->diffForHumans() ?? $conv->updated_at->diffForHumans(),
                    'unread' => 0, // Mock count or track per user later
                    'online' => true // Mock status indicator
                ];
            });

        return Inertia::render('Customer/Chat/Index', [
            'initialConversations' => $conversations
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
                    'sender_avatar' => $msg->sender->avatar,
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

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'text' => $request->text,
            'type' => $request->type,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
        ]);

        // Trigger dynamic simulated responses on the backend for demo flow!
        // If the sender is a Customer, have the Tech or Vendor respond automatically in the DB.
        if (auth()->user()->role === 'customer') {
            $text = strtolower($request->text);
            $responder = null;
            $replyText = null;

            // Pick a responder from the conversation peers
            $tech = $conversation->users->where('role', 'technician')->first();
            $vendor = $conversation->users->where('role', 'vendor')->first();

            if ($tech && $vendor) {
                if (str_contains($text, 'price') || str_contains($text, 'cost') || str_contains($text, 'quote') || str_contains($text, 'warranty') || str_contains($text, 'order') || str_contains($text, 'reserve')) {
                    $responder = $vendor;
                    $replyText = "Hi Clara, Sarah from EcoGrid here. I've logged your pricing/parts request in our factory inventory system. Let me prepare the invoice details for those parts.";
                } else {
                    $responder = $tech;
                    $replyText = "Got it! Marcus here. I will include this in my diagnostic notes and make sure the tools are ready for our service slot. Let me know if you need anything else.";
                }
            }

            if ($responder && $replyText) {
                // Save reply after a short delay (simulated in PHP, or created directly)
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $responder->id,
                    'text' => $replyText,
                    'type' => 'text',
                    'created_at' => now()->addSecond(), // 1s offset
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Message stored successfully'
        ]);
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
