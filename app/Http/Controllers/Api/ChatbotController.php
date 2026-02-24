<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotMessage;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    /**
     * Send a message to RealtyLinkPH Buddy and get a reply.
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Message is required and must be under 1000 characters.',
            ], 422);
        }

        $user = $request->user();
        $userMessage = trim($request->message);

        try {
            // Save user message
            ChatbotMessage::create([
                'user_id' => $user->id,
                'role' => 'user',
                'content' => $userMessage,
            ]);

            // Load conversation history for context
            $history = ChatbotMessage::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get()
                ->reverse()
                ->values()
                ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
                ->toArray();

            // Remove the last message (current one) since we pass it separately
            array_pop($history);

            // Call Gemini
            $gemini = new GeminiService();
            $reply = $gemini->chat($history, $userMessage);

            // Save assistant reply
            $assistantMsg = ChatbotMessage::create([
                'user_id' => $user->id,
                'role' => 'assistant',
                'content' => $reply,
            ]);

            return response()->json([
                'success' => true,
                'reply' => $reply,
                'message_id' => $assistantMsg->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reply' => 'Sorry, I encountered an error. Please try again.',
            ], 500);
        }
    }

    /**
     * Get chat history for the current user.
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $messages = ChatbotMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'role' => $m->role,
                'content' => $m->content,
                'created_at' => $m->created_at->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Clear chat history for the current user.
     */
    public function clearHistory(Request $request)
    {
        $user = $request->user();
        ChatbotMessage::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat history cleared.',
        ]);
    }
}
