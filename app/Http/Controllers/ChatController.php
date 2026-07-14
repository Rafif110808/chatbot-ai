<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use App\Models\AiSetting;

class ChatController extends Controller
{
    public function __construct(
        protected AiService $aiService
    ) {}

    public function index(): JsonResponse
    {
        $conversations = Conversation::where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json($conversations);
    }

    public function store(StoreConversationRequest $request): JsonResponse
    {
        $conversation = Conversation::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
        ]);

        return response()->json($conversation, 201);
    }

    public function show(Conversation $conversation): JsonResponse
    {
        if ($conversation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $messages = $conversation->messages()
            ->oldest()
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(StoreMessageRequest $request, Conversation $conversation): JsonResponse
    {
        if ($conversation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->message,
        ]);

        $isFirstMessage = $conversation->messages()->count() === 1;

        if ($isFirstMessage) {
            $conversation->update([
                'title' => str($request->message)->limit(50)->toString(),
            ]);
        }

        // Ambil riwayat pesan sebelumnya untuk konteks AI
        $history = $conversation->messages()
            ->oldest()
            ->where('id', '!=', $userMessage->id)
            ->get()
            ->toArray();

        $setting = AiSetting::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'temperature' => 0.7,
                'max_tokens' => 2048,
                'system_prompt' => null,
                'language' => 'indonesia',
                'answer_length' => 'medium',
            ]
        );

        $aiResponse = $this->aiService->generateResponse($request->message, $history, $setting);

        $aiMessage = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $aiResponse,
        ]);

        return response()->json($aiMessage, 201);
    }

    public function update(UpdateConversationRequest $request, Conversation $conversation): JsonResponse
    {
        if ($conversation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $conversation->update([
            'title' => $request->title,
        ]);

        return response()->json($conversation);
    }

    public function destroy(Conversation $conversation): JsonResponse
    {
        if ($conversation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $conversation->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
