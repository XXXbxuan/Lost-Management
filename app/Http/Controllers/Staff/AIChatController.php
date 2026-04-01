<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\AIActionService;
use App\Services\OpenAIChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class AIChatController extends Controller
{
    public function index(): View
    {
        return view('staff.ai_chat.index');
    }

    public function ask(
        Request $request,
        OpenAIChatService $chatService,
        AIActionService $actionService
    ): JsonResponse {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $action = $actionService->handle($validated['message']);

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

            $reply = $chatService->ask($validated['message']);

            return response()->json([
                'success' => true,
                'reply' => $reply,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'reply' => 'Sorry, the AI assistant is temporarily unavailable.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function clear(OpenAIChatService $chatService): JsonResponse
    {
        $chatService->clearHistory();

        return response()->json([
            'success' => true,
        ]);
    }
}