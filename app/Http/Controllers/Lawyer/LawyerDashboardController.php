<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class LawyerDashboardController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['user', 'messages' => function ($query) {
            $query->latest()->first();
        }])
        ->where('lawyer_id', Auth::id())
        ->latest()
        ->get()
        ->map(function ($conversation) {
            return [
                'id' => $conversation->id,
                'user' => $conversation->user,
                'status' => $conversation->status,
                'created_at' => $conversation->created_at,
                'last_message' => $conversation->messages->first(),
                'last_message_at' => $conversation->messages->first()?->created_at,
            ];
        });

        return view('lawyer.lawyer-dashboard', compact('conversations'));
    }

    public function getStats()
    {
        $stats = [
            'activeChats' => Conversation::where('lawyer_id', Auth::id())
                ->where('status', 'active')
                ->count(),
            'totalChats' => Conversation::where('lawyer_id', Auth::id())
                ->count(),
            'pendingChats' => Conversation::where('lawyer_id', Auth::id())
                ->where('status', 'pending')
                ->count(),
            'closedChats' => Conversation::where('lawyer_id', Auth::id())
                ->where('status', 'closed')
                ->count(),
        ];

        return response()->json($stats);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'conversation_ids' => 'required|array',
            'conversation_ids.*' => 'exists:conversations,id'
        ]);

        Conversation::whereIn('id', $request->conversation_ids)
            ->where('lawyer_id', Auth::id())
            ->delete();

        return response()->json(['success' => true]);
    }

    public function bulkClose(Request $request)
    {
        $request->validate([
            'conversation_ids' => 'required|array',
            'conversation_ids.*' => 'exists:conversations,id'
        ]);

        Conversation::whereIn('id', $request->conversation_ids)
            ->where('lawyer_id', Auth::id())
            ->update(['status' => 'closed']);

        return response()->json(['success' => true]);
    }

    public function destroy(Conversation $conversation)
    {
        if ($conversation->lawyer_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->delete();

        return response()->json(['success' => true]);
    }
}
