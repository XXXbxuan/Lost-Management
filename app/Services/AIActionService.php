<?php

namespace App\Services;

class AIActionService
{
    public function handle(string $message): ?array
    {
        $text = preg_replace('/\s+/', ' ', trim($message));
        $text = trim($text, " \t\n\r\0\x0B.,!?");

        if ($this->matches($text, [
            '/^open(?:\s+up)?\s+(?:the\s+)?lost\s+reports?(?:\s+(?:module|page|list))?$/i',
        ])) {
            return $this->openRecord(
                'Open Lost Reports Page',
                route('staff.lost-items.index'),
                'Click the button below to open the Lost Reports page.'
            );
        }

        if ($this->matches($text, [
            '/^open(?:\s+up)?\s+(?:the\s+)?(?:view|details?|report|view\s+report)\s+(?:for\s+)?lost\s+reports?\s*(?:id|no\.?|number)?\s*(\d+)$/i',
            '/^open(?:\s+up)?\s+(?:the\s+)?lost\s+reports?\s*(?:id|no\.?|number)?\s*(\d+)$/i',
            '/^open(?:\s+up)?\s+(?:the\s+)?lost\s+report\s+details?\s*(?:id|no\.?|number)?\s*(\d+)$/i',
            '/^open(?:\s+up)?\s+(?:the\s+)?view\s+report\s+lost\s+report\s*(?:id|no\.?|number)?\s*(\d+)$/i',
        ], $matches)) {
            $id = (int) $matches[1];

            return $this->openRecord(
                'Open View Report',
                route('staff.lost-items.index', ['open_report' => $id]),
                "I found lost report ID {$id}. Click the button below to open its view report modal."
            );
        }

        if ($this->matches($text, [
            '/^open(?:\s+up)?\s+(?:the\s+)?(?:candidate\s+match(?:ing)?|matching\s+page|match\s+page)\s+(?:for\s+)?lost\s+report\s*(?:id|no\.?|number)?\s*(\d+)$/i',
            '/^open(?:\s+up)?\s+lost\s+report\s*(?:id|no\.?|number)?\s*(\d+)\s+(?:matching|match)$/i',
            '/^open(?:\s+up)?\s+(?:the\s+)?matching\s+(?:for\s+)?lost\s+report\s*(?:id|no\.?|number)?\s*(\d+)$/i',
        ], $matches)) {
            $id = (int) $matches[1];

            return $this->openRecord(
                'Open Candidate Match Page',
                route('staff.lost-items.show', $id),
                "I found lost report ID {$id}. Click the button below to open its matching page."
            );
        }

        if ($this->matches($text, [
            '/^open(?:\s+up)?\s+(?:the\s+)?found\s+items?(?:\s+(?:module|page|list))?$/i',
        ])) {
            return $this->openRecord(
                'Open Found Items Page',
                route('staff.found-items.index'),
                'Click the button below to open the Found Items page.'
            );
        }

        if ($this->matches($text, [
            '/^open(?:\s+up)?\s+(?:the\s+)?(?:view|details?|view\s+item)\s+(?:for\s+)?found\s+items?\s*(?:id|no\.?|number)?\s*(\d+)$/i',
            '/^open(?:\s+up)?\s+(?:the\s+)?found\s+items?\s*(?:id|no\.?|number)?\s*(\d+)$/i',
            '/^open(?:\s+up)?\s+(?:the\s+)?view\s+found\s+item\s*(?:id|no\.?|number)?\s*(\d+)$/i',
        ], $matches)) {
            $id = (int) $matches[1];

            return $this->openRecord(
                'Open Found Item Details',
                route('staff.found-items.index', ['open_item' => $id]),
                "I found found item ID {$id}. Click the button below to open its details modal."
            );
        }

        if ($this->matches($text, [
            '/^open(?:\s+up)?\s+(?:the\s+)?claims?(?:\s+history)?(?:\s+(?:module|page|list))?$/i',
        ])) {
            return $this->openRecord(
                'Open Claims History',
                route('staff.claims.index'),
                'Click the button below to open the Claims History page.'
            );
        }

        if ($this->matches($text, [
            '/^open(?:\s+up)?\s+(?:the\s+)?(?:view\s+)?claim\s*(?:id|no\.?|number)?\s*(\d+)$/i',
            '/^open(?:\s+up)?\s+(?:the\s+)?claim\s+receipt\s*(?:for\s+)?(?:id|no\.?|number)?\s*(\d+)$/i',
            '/^open(?:\s+up)?\s+(?:the\s+)?claim\s+details?\s*(?:for\s+)?(?:id|no\.?|number)?\s*(\d+)$/i',
        ], $matches)) {
            $id = (int) $matches[1];

            return $this->openRecord(
                'Open Claim Receipt',
                route('staff.claims.index', ['open_claim' => $id]),
                "I found claim ID {$id}. Click the button below to open its receipt."
            );
        }

        if ($this->matches($text, [
            '/^open(?:\s+up)?\s+(?:the\s+)?match(?:\s+record)?\s*(?:id|no\.?|number)?\s*(\d+)$/i',
            '/^open(?:\s+up)?\s+(?:the\s+)?match\s+details?\s*(?:for\s+)?(?:id|no\.?|number)?\s*(\d+)$/i',
        ], $matches)) {
            $id = (int) $matches[1];

            return $this->openRecord(
                'Open Match Record',
                route('staff.claims.process', $id),
                "I found match record ID {$id}. Click the button below to open it."
            );
        }

        return null;
    }

    private function matches(string $text, array $patterns, ?array &$matches = null): bool
    {
        foreach ($patterns as $pattern) {
            $localMatches = [];

            if (preg_match($pattern, $text, $localMatches)) {
                $matches = $localMatches;
                return true;
            }
        }

        return false;
    }

    private function openRecord(string $label, string $url, string $message): array
    {
        return [
            'type' => 'open_record',
            'label' => $label,
            'url' => $url,
            'message' => $message,
        ];
    }
}