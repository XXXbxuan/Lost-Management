<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\SendAIChatMessageRequest;
use App\Services\AIActionService;
use App\Services\OpenAIChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AIChatController extends Controller
{
    protected OpenAIChatService $chatService;
    protected AIActionService $actionService;

    public function __construct(
        OpenAIChatService $chatService,
        AIActionService $actionService
    ) {
        $this->chatService = $chatService;
        $this->actionService = $actionService;
    }

    public function index(): View
    {
        return view('staff.ai_chat.index');
    }

    public function sendMessage(SendAIChatMessageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $action = $this->actionService->handle($validated['message']);

            if ($action) {
                return response()->json([
                    'success' => true,
                    'reply' => $action['message'],
                    'action' => [
                        'type' => $action['type'],
                        'label' => $action['label'],
                        'url' => $action['url'],
                    ],
                ]);
            }

            $reply = $this->chatService->ask($validated['message']);

            return response()->json([
                'success' => true,
                'reply' => $reply,
            ]);
        } catch (Throwable $e) {
            Log::error('AI chat sendMessage failed.', [
                'message' => $validated['message'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'reply' => 'Sorry, the AI assistant is temporarily unavailable.',
            ], 500);
        }
    }

    public function clearHistory(): JsonResponse
    {
        $this->chatService->clearHistory();

        return response()->json([
            'success' => true,
        ]);
    }
}