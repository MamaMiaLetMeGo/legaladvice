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
            $validated = $request->validate([
                'content' => 'required|string|max:1000',
                'new_conversation' => 'boolean',
                'conversation_id' => 'nullable|exists:conversations,id'
            ]);

            $conversationId = $request->input('conversation_id');
            
            if ($conversationId) {
                $conversation = Conversation::findOrFail($conversationId);
            } else {
                $conversation = Conversation::create([
                    'status' => 'pending',
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'last_message_at' => now(),
                ]);
            }

            $message = $conversation->messages()->create([
                'content' => $request->content,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
            ]);

            broadcast(new NewChatMessage($message->load('user')));

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'message' => $message->load('user')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send message'
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
        try {
            if ($conversation->status !== 'pending') {
                return response()->json([
                    'error' => 'Conversation is no longer available'
                ], 400);
            }

            $conversation->update([
                'lawyer_id' => auth()->id(),
                'status' => 'active'
            ]);

            $conversation->messages()->create([
                'content' => 'A legal expert has joined the conversation.',
                'system_message' => true,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'conversation' => $conversation->load('messages')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to claim conversation'
            ], 500);
        }
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

    public function lawyerSendMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'content' => 'required|string|max:1000',
            ]);

            $conversation = Conversation::findOrFail($request->conversation_id);

            $message = $conversation->messages()->create([
                'user_id' => auth()->id(),
                'content' => $request->content,
                'ip_address' => $request->ip()
            ]);

            $conversation->update([
                'last_message_at' => now()
            ]);

            $message->load('user');

            broadcast(new NewChatMessage($message))->toOthers();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send message'
            ], 500);
        }
    }
}
