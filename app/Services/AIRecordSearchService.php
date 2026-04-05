<?php

namespace App\Services;

use App\Models\FoundItem;
use App\Models\LostItemReport;
use App\Models\MatchRecord;

class AIRecordSearchService
{
    public function handle(string $message): ?string
    {
        $text = preg_replace('/\s+/', ' ', trim($message));
        $text = trim($text, " \t\n\r\0\x0B.,!?");

        $keyword = $this->extractKeyword($text);

        if (!$keyword || mb_strlen($keyword) < 2) {
            return null;
        }

        // Avoid treating module names as search keywords
        if ($this->isGenericModulePhrase($keyword)) {
            return null;
        }

        return $this->searchRelatedRecords($keyword);
    }

    private function extractKeyword(string $text): ?string
    {
        $keyword = null;

        $patterns = [
            '/^(?:find|search|look\s*for)\s+(.+)$/i',
            '/^help\s+me\s+find\s+(.+)$/i',
            '/^give\s+me\s+(.+)$/i',
            '/^show\s+me\s+(.+)$/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $keyword = trim($m[1]);
                break;
            }
        }

        if (!$keyword) {
            return null;
        }

        $keyword = preg_replace('/\brelated\b/i', '', $keyword);
        $keyword = preg_replace('/\brecords?\b/i', '', $keyword);
        $keyword = preg_replace('/\bresults?\b/i', '', $keyword);
        $keyword = preg_replace('/\bitems?\b$/i', '', $keyword);
        $keyword = preg_replace('/\breports?\b$/i', '', $keyword);
        $keyword = trim($keyword, " \t\n\r\0\x0B\"'");

        return trim($keyword);
    }

    private function isGenericModulePhrase(string $keyword): bool
    {
        $normalized = strtolower(trim($keyword));

        $genericPhrases = [
            'found',
            'lost',
            'match',
            'matches',
            'claim',
            'claims',
            'found item',
            'found items',
            'lost report',
            'lost reports',
            'match record',
            'match records',
            'claim history',
            'claim records',
        ];

        return in_array($normalized, $genericPhrases, true);
    }

    private function searchRelatedRecords(string $keyword): string
    {
        $foundItems = FoundItem::query()
            ->where(function ($query) use ($keyword) {
                $query->where('item_name', 'like', "%{$keyword}%")
                    ->orWhere('brand', 'like', "%{$keyword}%")
                    ->orWhere('category', 'like', "%{$keyword}%")
                    ->orWhere('color', 'like', "%{$keyword}%")
                    ->orWhere('serial_number', 'like', "%{$keyword}%")
                    ->orWhere('found_location', 'like', "%{$keyword}%")
                    ->orWhere('flight_number', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('status', 'like', "%{$keyword}%");
            })
            ->limit(5)
            ->get();

        $lostReports = LostItemReport::query()
            ->where(function ($query) use ($keyword) {
                $query->where('passenger_name', 'like', "%{$keyword}%")
                    ->orWhere('passenger_email', 'like', "%{$keyword}%")
                    ->orWhere('item_name', 'like', "%{$keyword}%")
                    ->orWhere('brand', 'like', "%{$keyword}%")
                    ->orWhere('category', 'like', "%{$keyword}%")
                    ->orWhere('color', 'like', "%{$keyword}%")
                    ->orWhere('serial_number', 'like', "%{$keyword}%")
                    ->orWhere('lost_location', 'like', "%{$keyword}%")
                    ->orWhere('flight_number', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('status', 'like', "%{$keyword}%");
            })
            ->limit(5)
            ->get();

        $matchRecords = MatchRecord::with(['lostItem', 'foundItem', 'claim'])
            ->where(function ($query) use ($keyword) {
                $query->whereHas('lostItem', function ($q) use ($keyword) {
                    $q->where('passenger_name', 'like', "%{$keyword}%")
                        ->orWhere('item_name', 'like', "%{$keyword}%")
                        ->orWhere('brand', 'like', "%{$keyword}%")
                        ->orWhere('category', 'like', "%{$keyword}%")
                        ->orWhere('color', 'like', "%{$keyword}%")
                        ->orWhere('status', 'like', "%{$keyword}%");
                })->orWhereHas('foundItem', function ($q) use ($keyword) {
                    $q->where('item_name', 'like', "%{$keyword}%")
                        ->orWhere('brand', 'like', "%{$keyword}%")
                        ->orWhere('category', 'like', "%{$keyword}%")
                        ->orWhere('color', 'like', "%{$keyword}%")
                        ->orWhere('found_location', 'like', "%{$keyword}%")
                        ->orWhere('status', 'like', "%{$keyword}%");
                });
            })
            ->limit(5)
            ->get();

        if ($foundItems->isEmpty() && $lostReports->isEmpty() && $matchRecords->isEmpty()) {
            return "I could not find any related record for '{$keyword}'.";
        }

        $lines = [];
        $lines[] = "I found related records for '{$keyword}'.";
        $lines[] = "";

        if ($foundItems->isNotEmpty()) {
            $lines[] = "Found Items:";
            foreach ($foundItems as $item) {
                $lines[] = "- found item id {$item->id} (item name: {$item->item_name}"
                    . ($item->brand ? ", brand: {$item->brand}" : "")
                    . ($item->status ? ", status: {$item->status}" : "")
                    . ")";
            }
            $lines[] = "";
        }

        if ($lostReports->isNotEmpty()) {
            $lines[] = "Lost Reports:";
            foreach ($lostReports as $report) {
                $lines[] = "- lost report id {$report->id} (item: {$report->item_name}, passenger: {$report->passenger_name}, status: {$report->status})";
            }
            $lines[] = "";
        }

        if ($matchRecords->isNotEmpty()) {
            $lines[] = "Related Match / Claim Records:";
            foreach ($matchRecords as $match) {
                $lines[] = "- match id {$match->id} (lost: "
                    . ($match->lostItem?->item_name ?? '-')
                    . ", found: " . ($match->foundItem?->item_name ?? '-')
                    . ", claim: " . ($match->claim ? '#' . $match->claim->id : 'None')
                    . ")";
            }
            $lines[] = "";
        }

        $lines[] = "Which one do you want to open?";
        $lines[] = "Reply like: open found item id 3";
        $lines[] = "Or: open lost report id 3";
        $lines[] = "Or: open match id 1";
        $lines[] = "Or: open claim id 1";

        return implode("\n", $lines);
    }
}