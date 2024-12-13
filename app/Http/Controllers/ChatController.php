<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function getConversation()
    {
        $conversation = null;
        $messages = [];

        if (auth()->check()) {
            $conversation = Conversation::where('user_id', auth()->id())
                ->where('status', '!=', 'closed')
                ->latest()
                ->first();

            if ($conversation) {
                $messages = $conversation->messages()->with('user')->get();
            }
        }

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        try {
            \Log::info('Starting sendMessage with request:', $request->all());

            $validated = $request->validate([
                'content' => 'required|string|max:1000',
                'conversation_id' => 'nullable|exists:conversations,id',
                'new_conversation' => 'required|boolean'
            ]);

            DB::beginTransaction();

            if ($request->new_conversation) {
                $conversation = Conversation::create([
                    'user_id' => auth()->check() ? auth()->id() : null,
                    'status' => 'pending',
                    'ip_address' => $request->ip(),
                    'last_message_at' => now()
                ]);
            } else {
                $conversation = Conversation::findOrFail($request->conversation_id);
                $conversation->update(['last_message_at' => now()]);
            }

            $message = $conversation->messages()->create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'content' => $request->content,
                'ip_address' => $request->ip()
            ]);

            DB::commit();

            $message->load('user');
            $conversation->load('lawyer');

            broadcast(new NewChatMessage($message))->toOthers();

            return response()->json([
                'message' => $message,
                'conversation' => $conversation
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in sendMessage:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to send message',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getMessages(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        
        return response()->json([
            'messages' => $conversation->messages()->with('user')->latest()->paginate(50),
            'conversation' => $conversation->load('lawyer', 'user')
        ]);
    }

    public function claimConversation(Conversation $conversation)
    {
        if ($conversation->status !== 'pending') {
            return response()->json(['error' => 'This conversation is no longer available'], 400);
        }

        $conversation->update([
            'lawyer_id' => auth()->id(),
            'status' => 'active'
        ]);

        // Create a system message to notify the user
        $conversation->messages()->create([
            'content' => 'A legal expert has joined the conversation.',
            'system_message' => true
        ]);

        broadcast(new ConversationUpdated($conversation))->toOthers();

        return response()->json([
            'message' => 'Conversation claimed successfully',
            'conversation' => $conversation->load('messages')
        ]);
    }

    public function getPendingConversations()
    {
        return Conversation::where('status', 'pending')
            ->with(['messages' => function ($query) {
                $query->latest()->take(1);
            }])
            ->latest()
            ->get();
    }

    public function getActiveConversations()
    {
        return Conversation::where('status', 'active')
            ->where('lawyer_id', auth()->id())
            ->with(['messages' => function ($query) {
                $query->latest()->take(1);
            }])
            ->latest()
            ->get();
    }

    public function index()
    {
        return view('chat.index', [
            'userId' => auth()->id()
        ]);
    }

    public function closeConversation(Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $conversation->update([
            'status' => 'closed',
        ]);

        // Broadcast that the conversation was closed
        broadcast(new ConversationClosed($conversation))->toOthers();

        return response()->json(['message' => 'Conversation closed successfully']);
    }

    public function showConversation(Conversation $conversation)
    {
        if ($conversation->lawyer_id !== auth()->id()) {
            return redirect()->route('lawyer.dashboard')
                ->with('error', 'You do not have access to this conversation.');
        }

        return view('lawyer.conversation', [
            'conversation' => $conversation->load(['messages.user', 'user']),
        ]);
    }
}
