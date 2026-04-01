<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use RuntimeException;

class OpenAIChatService
{
    public function __construct(
        protected AIRecordLookupService $recordLookupService,
        protected AIRecordSearchService $recordSearchService
    ) {
    }

    public function ask(string $message): string
    {
        $message = trim($message);

        $searchAnswer = $this->recordSearchService->handle($message);
        if ($searchAnswer) {
            $this->storeHistory($message, $searchAnswer);
            return $searchAnswer;
        }

        $lookupAnswer = $this->recordLookupService->handle($message);
        if ($lookupAnswer) {
            $this->storeHistory($message, $lookupAnswer);
            return $lookupAnswer;
        }

        $ruleBasedAnswer = $this->tryRuleBasedAnswer($message);
        if ($ruleBasedAnswer) {
            $this->storeHistory($message, $ruleBasedAnswer);
            return $ruleBasedAnswer;
        }

        $apiKey = config('services.groq.api_key');
        $model = config('services.groq.model', 'openai/gpt-oss-20b');

        if (!$apiKey) {
            throw new RuntimeException('Groq API key is not configured.');
        }

        $history = Session::get('ai_chat_history', []);
        $knowledge = config('alims_ai');

        $systemPrompt = $this->buildSystemPrompt($knowledge);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach (array_slice($history, -6) as $item) {
            $messages[] = [
                'role' => $item['role'],
                'content' => $item['content'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.3,
                'max_tokens' => 400,
            ]);

        Log::info('Groq raw response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Groq request failed: ' . $response->body());
        }

        $reply = data_get(
            $response->json(),
            'choices.0.message.content',
            'Sorry, no response was returned.'
        );

        $reply = $this->cleanReply($reply);
        $this->storeHistory($message, $reply);

        return $reply;
    }

    public function clearHistory(): void
    {
        Session::forget('ai_chat_history');
    }

    private function tryRuleBasedAnswer(string $message): ?string
    {
        $text = strtolower($message);
        $faq = config('alims_ai.faq', []);

        foreach ($faq as $item) {
            foreach ($item['keywords'] as $keyword) {
                if (str_contains($text, strtolower($keyword))) {
                    return $item['answer'];
                }
            }
        }

        if (str_contains($text, 'what is my system') || str_contains($text, 'what is alims')) {
            return 'ALIMS is the Airport Lost and Found Management System. It helps staff manage lost reports, found items, matching, claim processing, inventory tracking, and claim history in one system.';
        }

        return null;
    }

    private function buildSystemPrompt(array $knowledge): string
    {
        $modulesText = '';
        foreach (($knowledge['modules'] ?? []) as $name => $desc) {
            $modulesText .= "- {$name}: {$desc}\n";
        }

        $statusText = '';
        foreach (($knowledge['status_meanings'] ?? []) as $name => $desc) {
            $statusText .= "- {$name}: {$desc}\n";
        }

        $rulesText = '';
        foreach (($knowledge['rules']['lost_reports'] ?? []) as $rule) {
            $rulesText .= "- Lost Reports: {$rule}\n";
        }
        foreach (($knowledge['rules']['found_items'] ?? []) as $rule) {
            $rulesText .= "- Found Items: {$rule}\n";
        }

        $workflowText = '';
        foreach (($knowledge['workflow'] ?? []) as $step) {
            $workflowText .= "- {$step}\n";
        }

        return <<<PROMPT
You are the ALIMS AI Help Assistant.

Only answer within ALIMS system scope.
Be concise, clear, and practical.
Do not invent features that do not exist.
Do not use markdown symbols like ** or ##.
Use short paragraphs or short numbered steps when helpful.

System name:
{$knowledge['system_name']}

Modules:
{$modulesText}

Status meanings:
{$statusText}

Rules:
{$rulesText}

Workflow:
{$workflowText}
PROMPT;
    }

    private function cleanReply(string $text): string
    {
        $text = str_replace(['**', '##', '###', '`'], '', $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        return trim($text);
    }

    private function storeHistory(string $userMessage, string $reply): void
    {
        $history = Session::get('ai_chat_history', []);
        $history[] = ['role' => 'user', 'content' => $userMessage];
        $history[] = ['role' => 'assistant', 'content' => $reply];
        Session::put('ai_chat_history', array_slice($history, -10));
    }
}