<?php

namespace App\Services;

class AIActionService
{
    public function handle(string $message): ?array
    {
        $text = trim($message);

        // Open Lost Reports module page
        if (preg_match('/^open\s+lost\s+reports?(?:\s+page)?$/i', $text)) {
            return [
                'type' => 'open_record',
                'label' => 'Open Lost Reports Page',
                'url' => route('staff.lost-items.index'),
                'message' => 'Click the button below to open the Lost Reports page.',
            ];
        }

        // Open Found Items module page
        if (preg_match('/^open\s+found\s+items?(?:\s+page)?$/i', $text)) {
            return [
                'type' => 'open_record',
                'label' => 'Open Found Items Page',
                'url' => route('staff.found-items.index'),
                'message' => 'Click the button below to open the Found Items page.',
            ];
        }

        // Open Claims History module page
        if (preg_match('/^open\s+claims?(?:\s+history)?(?:\s+page)?$/i', $text)) {
            return [
                'type' => 'open_record',
                'label' => 'Open Claims History',
                'url' => route('staff.claims.index'),
                'message' => 'Click the button below to open the Claims History page.',
            ];
        }

        // Open View Report modal in Lost Reports list
        if (preg_match('/^open\s+view\s+report\s+lost\s+report\s+id\s+(\d+)$/i', $text, $m)) {
            $id = (int) $m[1];

            return [
                'type' => 'open_record',
                'label' => 'Open View Report',
                'url' => route('staff.lost-items.index', ['open_report' => $id]),
                'message' => "I found lost report ID {$id}. Click the button below to open its view report modal.",
            ];
        }

        // Open candidate match page for lost report
        if (preg_match('/^open\s+lost\s+report\s+id\s+(\d+)$/i', $text, $m)) {
            $id = (int) $m[1];

            return [
                'type' => 'open_record',
                'label' => 'Open Candidate Match Page',
                'url' => route('staff.lost-items.show', $id),
                'message' => "I found lost report ID {$id}. Click the button below to open its matching page.",
            ];
        }

        // Open Found Item details modal
        if (preg_match('/^open\s+view\s+found\s+item\s+id\s+(\d+)$/i', $text, $m)) {
            $id = (int) $m[1];

            return [
                'type' => 'open_record',
                'label' => 'Open Found Item Details',
                'url' => route('staff.found-items.index', ['open_item' => $id]),
                'message' => "I found found item ID {$id}. Click the button below to open its details modal.",
            ];
        }

        if (preg_match('/^open\s+found\s+item\s+id\s+(\d+)$/i', $text, $m)) {
            $id = (int) $m[1];

            return [
                'type' => 'open_record',
                'label' => 'Open Found Item Details',
                'url' => route('staff.found-items.index', ['open_item' => $id]),
                'message' => "I found found item ID {$id}. Click the button below to open its details modal.",
            ];
        }

        // Open Claim receipt
        if (preg_match('/^open\s+view\s+claim\s+id\s+(\d+)$/i', $text, $m)) {
            $id = (int) $m[1];

            return [
                'type' => 'open_record',
                'label' => 'Open Claim Receipt',
                'url' => route('staff.claims.index', ['open_claim' => $id]),
                'message' => "I found claim ID {$id}. Click the button below to open its receipt.",
            ];
        }

        if (preg_match('/^open\s+claim\s+id\s+(\d+)$/i', $text, $m)) {
            $id = (int) $m[1];

            return [
                'type' => 'open_record',
                'label' => 'Open Claim Receipt',
                'url' => route('staff.claims.index', ['open_claim' => $id]),
                'message' => "I found claim ID {$id}. Click the button below to open its receipt.",
            ];
        }

        // Open Match record page
        if (preg_match('/^open\s+match\s+id\s+(\d+)$/i', $text, $m)) {
            $id = (int) $m[1];

            return [
                'type' => 'open_record',
                'label' => 'Open Match Record',
                'url' => route('staff.claims.process', $id),
                'message' => "I found match record ID {$id}. Click the button below to open it.",
            ];
        }

        return null;
    }
}