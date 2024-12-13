<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'conversation_id' => 'required_without:new_conversation|exists:conversations,id',
            'new_conversation' => 'required_without:conversation_id|boolean'
        ]);

        if ($request->new_conversation) {
            $conversation = Conversation::create([
                'user_id' => auth()->id(),
                'status' => 'pending'
            ]);
        } else {
            $conversation = Conversation::findOrFail($request->conversation_id);
        }

        $message = $conversation->messages()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Broadcast the new message
        broadcast(new NewMessage($message))->toOthers();

        return response()->json($message->load('user'));
    }

    public function getMessages(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        
        return response()->json([
            'messages' => $conversation->messages()->with('user')->latest()->paginate(50),
            'conversation' => $conversation->load('lawyer', 'user')
        ]);
    }
}
