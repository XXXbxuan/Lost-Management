<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\LostItemReport;
use App\Models\MatchRecord;

class AIRecordSearchService
{
    public function handle(string $message): ?string
    {
        $text = trim($message);

        $keyword = null;

        if (preg_match('/^(?:find|search|look\s*for)\s+(.+)$/i', $text, $m)) {
            $keyword = trim($m[1]);
        }

        if (!$keyword && preg_match('/^help me find\s+(.+)$/i', $text, $m)) {
            $keyword = trim($m[1]);
        }

        if (!$keyword && preg_match('/^give me\s+(.+)$/i', $text, $m)) {
            $keyword = trim($m[1]);
        }

        if (!$keyword && preg_match('/^show me\s+(.+)$/i', $text, $m)) {
            $keyword = trim($m[1]);
        }

        if ($keyword) {
            $keyword = preg_replace('/\brelated\b/i', '', $keyword);
            $keyword = preg_replace('/\brecords?\b/i', '', $keyword);
            $keyword = preg_replace('/\bitems?\b/i', '', $keyword);
            $keyword = trim($keyword);
        }

        if (!$keyword || strlen($keyword) < 2) {
            return null;
        }

        return $this->searchRelatedRecords($keyword);
    }

    private function searchRelatedRecords(string $keyword): string
    {
        $foundItems = FoundItem::query()
            ->where('item_name', 'like', "%{$keyword}%")
            ->orWhere('brand', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->limit(5)
            ->get();

        $lostReports = LostItemReport::query()
            ->where('passenger_name', 'like', "%{$keyword}%")
            ->orWhere('item_name', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->limit(5)
            ->get();

        $matchRecords = MatchRecord::with(['lostItem', 'foundItem', 'claim'])
            ->whereHas('lostItem', function ($q) use ($keyword) {
                $q->where('passenger_name', 'like', "%{$keyword}%")
                  ->orWhere('item_name', 'like', "%{$keyword}%");
            })
            ->orWhereHas('foundItem', function ($q) use ($keyword) {
                $q->where('item_name', 'like', "%{$keyword}%")
                  ->orWhere('brand', 'like', "%{$keyword}%");
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
                $lines[] = "- found item id {$item->id} (item name: {$item->item_name}" .
                    ($item->brand ? ", brand: {$item->brand}" : "") .
                    ($item->status ? ", status: {$item->status}" : "") . ")";
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
                $lines[] = "- match id {$match->id} (lost: " . ($match->lostItem?->item_name ?? '-') .
                    ", found: " . ($match->foundItem?->item_name ?? '-') .
                    ", claim: " . ($match->claim ? '#' . $match->claim->id : 'None') . ")";
            }
            $lines[] = "";
        }

        $lines[] = "Which one do you want to open?";
        $lines[] = "Reply like: found item id 3";
        $lines[] = "Or: lost report id 3";
        $lines[] = "Or: match id 1";
        $lines[] = "Or: claim id 1";

        return implode("\n", $lines);
    }
}