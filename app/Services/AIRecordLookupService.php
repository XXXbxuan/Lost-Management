<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\LostItemReport;
use App\Models\MatchRecord;
use App\Models\Staff;

class AIRecordLookupService
{
    public function handle(string $message): ?string
    {
        $text = preg_replace('/\s+/', ' ', trim($message));
        $text = trim($text, " \t\n\r\0\x0B.,!?");

        if ($this->matches($text, [
            '/^(?:show|view|get|check)?\s*(?:staff|staff\s+summary|staff\s+member)(?:\s*(?:id|no\.?|number))?\s*#?\s*([A-Za-z0-9_-]+)$/i',
        ], $m)) {
            return $this->getStaffSummary($m[1]);
        }

        if ($this->matches($text, [
            '/^(?:show|view|get|check)?\s*(?:lost\s+report|lost)(?:\s*(?:id|no\.?|number))?\s*#?\s*(\d+)$/i',
            '/^(?:show|view|get|check)?\s*lost\s+report\s+details?(?:\s*(?:id|no\.?|number))?\s*#?\s*(\d+)$/i',
        ], $m)) {
            return $this->getLostReportSummary((int) $m[1]);
        }

        if ($this->matches($text, [
            '/^(?:show|view|get|check)?\s*(?:found\s+item|found)(?:\s*(?:id|no\.?|number))?\s*#?\s*(\d+)$/i',
            '/^(?:show|view|get|check)?\s*found\s+item\s+details?(?:\s*(?:id|no\.?|number))?\s*#?\s*(\d+)$/i',
        ], $m)) {
            return $this->getFoundItemSummary((int) $m[1]);
        }

        if ($this->matches($text, [
            '/^(?:show|view|get|check)?\s*(?:match|match\s+record)(?:\s*(?:id|no\.?|number))?\s*#?\s*(\d+)$/i',
            '/^(?:show|view|get|check)?\s*match\s+record\s+details?(?:\s*(?:id|no\.?|number))?\s*#?\s*(\d+)$/i',
        ], $m)) {
            return $this->getMatchSummary((int) $m[1]);
        }

        return null;
    }

    private function getStaffSummary(string $input): string
    {
        $staff = Staff::with('user')
            ->where(function ($query) use ($input) {
                $query->where('staff_id', $input)
                    ->orWhere('staff_id', 'like', '%' . $input . '%');
            })
            ->first();

        if (!$staff) {
            return "I could not find any staff record matching staff ID {$input}.";
        }

        $lostReports = LostItemReport::where('staff_id', $staff->staff_id);
        $foundItems = FoundItem::where('staff_id', $staff->staff_id);

        $lostCount = (clone $lostReports)->count();
        $foundCount = (clone $foundItems)->count();

        $lostLost = (clone $lostReports)->whereIn('status', ['LOST', 'Lost'])->count();
        $lostMatched = (clone $lostReports)->whereIn('status', ['Matched', 'Reschedule Requested'])->count();
        $lostClaimed = (clone $lostReports)->where('status', 'Claimed')->count();

        $foundUnclaimed = (clone $foundItems)->where('status', 'Unclaimed')->count();
        $foundMatched = (clone $foundItems)->where('status', 'Matched')->count();
        $foundClaimed = (clone $foundItems)->where('status', 'Claimed')->count();
        $foundRemoved = (clone $foundItems)->where('status', 'Removed')->count();

        $latestLost = (clone $lostReports)->latest('created_at')->first();
        $latestFound = (clone $foundItems)->latest('created_at')->first();

        $name = $staff->name ?? $staff->user?->name ?? 'N/A';
        $email = $staff->email ?? $staff->user?->email ?? 'N/A';
        $role = $staff->role ?? $staff->user?->role ?? 'N/A';

        return
            "Staff Summary\n\n" .
            "Name: {$name}\n" .
            "Staff ID: {$staff->staff_id}\n" .
            "Email: {$email}\n" .
            "Role: {$role}\n\n" .
            "Lost Reports Created: {$lostCount}\n" .
            "LOST: {$lostLost}\n" .
            "Matched / Reschedule: {$lostMatched}\n" .
            "Claimed: {$lostClaimed}\n\n" .
            "Found Items Registered: {$foundCount}\n" .
            "Unclaimed: {$foundUnclaimed}\n" .
            "Matched: {$foundMatched}\n" .
            "Claimed: {$foundClaimed}\n" .
            "Removed: {$foundRemoved}\n\n" .
            "Latest Lost Report: " . ($latestLost ? "#{$latestLost->id} - {$latestLost->item_name}" : 'None') . "\n" .
            "Latest Found Item: " . ($latestFound ? "#{$latestFound->id} - {$latestFound->item_name}" : 'None');
    }

    private function getLostReportSummary(int $id): string
    {
        $report = LostItemReport::with('staff.user')->find($id);

        if (!$report) {
            return "I could not find lost report ID {$id}.";
        }

        $match = MatchRecord::where('lostId', $report->id)
            ->latest('created_at')
            ->first();

        $claim = $match
            ? Claim::where('match_id', $match->id)->latest('created_at')->first()
            : null;

        $staffName = $report->staff?->name ?? $report->staff?->user?->name ?? 'N/A';

        return
            "Lost Report Summary\n\n" .
            "Report ID: {$report->id}\n" .
            "Item Name: {$report->item_name}\n" .
            "Category: {$report->category}\n" .
            "Color: " . ($report->color ?: '-') . "\n" .
            "Brand: " . ($report->brand ?: '-') . "\n" .
            "Serial Number: " . ($report->serial_number ?: '-') . "\n" .
            "Passenger Name: {$report->passenger_name}\n" .
            "Passenger Email: " . ($report->passenger_email ?: '-') . "\n" .
            "Passenger Phone: " . ($report->passenger_phone ?: '-') . "\n" .
            "Lost Location: {$report->lost_location}\n" .
            "Flight Number: " . ($report->flight_number ?: '-') . "\n" .
            "Lost Time: " . ($report->lost_time ?: '-') . "\n" .
            "Status: {$report->status}\n" .
            "Created By: {$staffName}\n" .
            "Description: " . ($report->description ?: 'No description provided.') . "\n" .
            "Match Record: " . ($match ? "#{$match->id}" : 'None') . "\n" .
            "Claim Record: " . ($claim ? "#{$claim->id}" : 'None');
    }

    private function getFoundItemSummary(int $id): string
    {
        $item = FoundItem::with('staff.user')->find($id);

        if (!$item) {
            return "I could not find found item ID {$id}.";
        }

        $match = MatchRecord::where('foundId', $item->id)
            ->latest('created_at')
            ->first();

        $claim = $match
            ? Claim::where('match_id', $match->id)->latest('created_at')->first()
            : null;

        $staffName = $item->staff?->name ?? $item->staff?->user?->name ?? 'N/A';

        return
            "Found Item Summary\n\n" .
            "Item ID: {$item->id}\n" .
            "Item Name: {$item->item_name}\n" .
            "Category: {$item->category}\n" .
            "Color: " . ($item->color ?: '-') . "\n" .
            "Brand: " . ($item->brand ?: '-') . "\n" .
            "Serial Number: " . ($item->serial_number ?: '-') . "\n" .
            "Found Location: {$item->found_location}\n" .
            "Flight Number: " . ($item->flight_number ?: '-') . "\n" .
            "Found Time: " . ($item->found_time ?: '-') . "\n" .
            "Storage Location: " . ($item->storage_location ?: '-') . "\n" .
            "Status: {$item->status}\n" .
            "Registered By: {$staffName}\n" .
            "Description: " . ($item->description ?: 'No description provided.') . "\n" .
            "Match Record: " . ($match ? "#{$match->id}" : 'None') . "\n" .
            "Claim Record: " . ($claim ? "#{$claim->id}" : 'None');
    }

    private function getMatchSummary(int $id): string
    {
        $match = MatchRecord::with(['lostItem', 'foundItem', 'claim'])->find($id);

        if (!$match) {
            return "I could not find match record ID {$id}.";
        }

        $claim = Claim::where('match_id', $match->id)->latest('created_at')->first();

        return
            "Match Record Summary\n\n" .
            "Match ID: {$match->id}\n" .
            "Lost Report ID: " . ($match->lostId ?: '-') . "\n" .
            "Found Item ID: " . ($match->foundId ?: '-') . "\n" .
            "Lost Item Name: " . ($match->lostItem?->item_name ?: '-') . "\n" .
            "Found Item Name: " . ($match->foundItem?->item_name ?: '-') . "\n" .
            "Status: " . ($match->status ?: '-') . "\n" .
            "Similarity Score: " . ($match->similarityScore ?? '-') . "\n" .
            "Appointment At: " . ($match->appointment_at ?: '-') . "\n" .
            "Appointment Venue: " . ($match->appointment_venue ?: '-') . "\n" .
            "Confirmed: " . ($match->is_confirmed ? 'Yes' : 'No') . "\n" .
            "Claim Record: " . ($claim ? "#{$claim->id}" : 'None') . "\n" .
            "Notes: " . ($match->notes ?: 'No notes.');
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
}