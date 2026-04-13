<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreLostItemReportRequest;
use App\Http\Requests\Staff\StoreMatchVerificationRequest;
use App\Http\Requests\Staff\UpdateLostItemReportRequest;
use App\Models\AdminActionLog;
use App\Models\FoundItem;
use App\Models\LostItemReport;
use App\Models\MatchRecord;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;

class LostItemController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'All');
        $openReportId = $request->query('open_report');

        $query = LostItemReport::query()->latest();
        $perPage = 10;

        if ($status !== 'All') {
            $query->where('status', $status);
        }

        if ($openReportId) {
            $orderedIds = (clone $query)->pluck('id')->values();

            $position = $orderedIds->search(function ($id) use ($openReportId) {
                return (string) $id === (string) $openReportId;
            });

            if ($position !== false) {
                $targetPage = (int) floor($position / $perPage) + 1;

                Paginator::currentPageResolver(function () use ($targetPage) {
                    return $targetPage;
                });
            }
        }

        $lostItems = $query->paginate($perPage)->appends($request->query());

        return view('staff.lost_reports.index', compact('lostItems', 'status', 'openReportId'));
    }

    public function create(): View
    {
        return view('staff.lost_reports.create');
    }

    public function store(StoreLostItemReportRequest $request): RedirectResponse
    {
        $validated = $this->normalizeMultiColor($request->validated());

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('lost_reports', 'public');
        }

        if (auth()->check()) {
            $staff = Staff::where('user_id', auth()->id())->first();

            if (!$staff) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'staff_id' => 'No staff record found for the current logged-in user.',
                    ]);
            }

            $validated['staff_id'] = $staff->staff_id;
        }

        $lostItem = LostItemReport::create($validated);

        $this->writeActionLog(
            'CREATE_LOST_REPORT',
            "Report #{$lostItem->id}",
            "Passenger: {$lostItem->passenger_name}, Item: {$lostItem->item_name} ({$lostItem->category})."
        );

        return redirect()
            ->route('dashboard')
            ->with('success', 'Lost item report submitted successfully. The system will start matching.');
    }

    public function edit(LostItemReport $lostItem): RedirectResponse|View
    {
        if (!$this->isEditableLostReport($lostItem)) {
            return redirect()
                ->route('staff.lost-items.index')
                ->with('error', 'Only unsolved lost reports can be edited.');
        }

        return view('staff.lost_reports.edit', compact('lostItem'));
    }

    public function update(UpdateLostItemReportRequest $request, LostItemReport $lostItem): RedirectResponse
    {
        if (!$this->isEditableLostReport($lostItem)) {
            return redirect()
                ->route('staff.lost-items.index')
                ->with('error', 'Only unsolved lost reports can be updated.');
        }

        $validated = $this->normalizeMultiColor($request->validated());

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('lost_reports', 'public');
        }

        unset($validated['sub_colors']);

        $lostItem->update($validated);

        $this->writeActionLog(
            'UPDATE_LOST_REPORT',
            "Report #{$lostItem->id}",
            "Updated lost report for item: {$lostItem->item_name}."
        );

        return redirect()
            ->route('staff.lost-items.index')
            ->with('success', 'Lost report updated successfully.');
    }

    public function destroy(LostItemReport $lostItem): RedirectResponse
    {
        if (!$this->isEditableLostReport($lostItem)) {
            return redirect()
                ->route('staff.lost-items.index')
                ->with('error', 'Only unsolved lost reports can be deleted.');
        }

        $this->writeActionLog(
            'DELETE_LOST_REPORT',
            "Report #{$lostItem->id}",
            "Deleted lost report for item: {$lostItem->item_name}."
        );

        $lostItem->delete();

        return redirect()
            ->route('staff.lost-items.index')
            ->with('success', 'Lost report deleted successfully.');
    }

    public function show(int $id, Request $request): View
    {
        $lostItem = LostItemReport::findOrFail($id);

        $rejectedIds = MatchRecord::where('lostId', $id)
            ->where('status', 'Rejected')
            ->pluck('foundId');

        $query = FoundItem::where('status', 'Unclaimed')
            ->whereNotIn('id', $rejectedIds);

        $isManualSearch = $request->has('search');

        if ($isManualSearch) {
            if ($request->filled('category')) {
                $query->where('category', $request->input('category'));
            }

            if ($request->filled('location')) {
                $query->where('found_location', $request->input('location'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('found_time', '>=', $request->input('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('found_time', '<=', $request->input('date_to'));
            }

            if ($request->filled('keyword')) {
                $search = $request->input('keyword');

                $query->where(function ($q) use ($search) {
                    $q->where('item_name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('color', 'LIKE', "%{$search}%");
                });
            }
        } else {

            if ($lostItem->lost_time) {
                $query->whereDate('found_time', '>=', $lostItem->lost_time->format('Y-m-d'));
            }
        }

        $textQuery = trim(implode(' ', array_filter([
            $lostItem->item_name,
            $lostItem->brand,
            $lostItem->description,
        ])));

        $candidateMatches = $query
            ->select('found_items.*')
            ->selectRaw(
                "MATCH(item_name, brand, description) AGAINST (? IN NATURAL LANGUAGE MODE) as text_relevance",
                [$textQuery]
            )
            ->latest()
            ->get();

        foreach ($candidateMatches as $item) {
            $item->similarity_score = $this->calculateSimilarityScore($lostItem, $item);
        }

        if (!$isManualSearch) {
            $candidateMatches = $candidateMatches->filter(fn ($item) => $item->similarity_score >= 50);
        }

        $candidateMatches = $candidateMatches->sortByDesc('similarity_score');
        $rejectedItems = FoundItem::whereIn('id', $rejectedIds)->get();

        return view('staff.lost_reports.show', compact('lostItem', 'candidateMatches', 'rejectedItems'));
    }

    public function showMatchVerification(int $lost_id, int $found_id, Request $request): View
    {
        $lostItem = LostItemReport::findOrFail($lost_id);
        $foundItem = FoundItem::findOrFail($found_id);
        $score = $request->query('score', 0);

        return view('staff.lost_reports.verify', compact('lostItem', 'foundItem', 'score'));
    }

    public function storeMatchVerification(StoreMatchVerificationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $score = $validated['similarity_score'] ?? 0;
        $status = $validated['outcome'] === 'matched' ? 'Verified' : 'Rejected';

        MatchRecord::updateOrCreate(
            [
                'lostId' => $validated['lost_id'],
                'foundId' => $validated['found_id'],
            ],
            [
                'notes' => $validated['notes'],
                'status' => $status,
                'verifiedBy' => auth()->id(),
                'verifiedAt' => now(),
                'similarityScore' => $score,
            ]
        );

        $action = $validated['outcome'] === 'matched' ? 'VERIFY_MATCH' : 'REJECT_MATCH';

        if ($validated['outcome'] === 'not_matched' && ($validated['source'] ?? null) === 'reject_claim') {
            $action = 'REJECT_CLAIM';
        }

        $this->writeActionLog(
            $action,
            "Lost #{$validated['lost_id']} vs Found #{$validated['found_id']}",
            "Result: " . ucfirst($validated['outcome']) . ", Score: {$score}%. Note: {$validated['notes']}"
        );

        if ($validated['outcome'] === 'matched') {
            LostItemReport::where('id', $validated['lost_id'])->update(['status' => 'Matched']);
            FoundItem::where('id', $validated['found_id'])->update(['status' => 'Matched']);
        } else {
            LostItemReport::where('id', $validated['lost_id'])->update(['status' => 'LOST']);

            $found = FoundItem::find($validated['found_id']);
            if ($found && $found->status === 'Matched') {
                $found->update(['status' => 'Unclaimed']);
            }
        }

        if (!empty($validated['return_url'])) {
            return redirect()->to($validated['return_url'])->with('success', 'Verification record saved.');
        }

        return redirect()
            ->route('staff.lost-items.index')
            ->with('success', 'Verification record saved.');
    }

    public function undoMatch(int $lostId): RedirectResponse
    {
        $match = MatchRecord::where('lostId', $lostId)->first();

        if ($match) {
            $this->writeActionLog(
                'UNDO_MATCH',
                "Match Record #{$match->id}",
                "Action: Unlinked Lost Item #{$match->lostId} from Found Item #{$match->foundId}. Status reset."
            );

            FoundItem::where('id', $match->foundId)->update(['status' => 'Unclaimed']);
            LostItemReport::where('id', $lostId)->update(['status' => 'LOST']);
            $match->delete();
        }

        return back()->with('success', 'Match has been cancelled.');
    }

    public function renderTimelineHtml(int $id): string
    {
        $lostItem = LostItemReport::findOrFail($id);

        $match = MatchRecord::where('lostId', $lostItem->id)
            ->where('status', 'Verified')
            ->first();

        if (!$match || !$match->foundItem) {
            return '<div class="p-6 text-center text-gray-500">No verified timeline data available.</div>';
        }

        $foundItem = $match->foundItem;

        return view('staff.claims.partials.timeline', compact('foundItem', 'match'))->render();
    }

    private function isEditableLostReport(LostItemReport $lostItem): bool
    {
        return in_array($lostItem->status, ['LOST', 'Lost']);
    }

    private function normalizeMultiColor(array $validated): array
    {
        if (!empty($validated['sub_colors']) && is_array($validated['sub_colors'])) {
            $subColors = array_filter(array_map('trim', $validated['sub_colors']));

            if (($validated['color'] ?? '') === 'Multi-color' && count($subColors) > 0) {
                $validated['color'] = 'Multi-color (' . implode(', ', $subColors) . ')';
            }
        }

        return $validated;
    }

    private function calculateSimilarityScore(LostItemReport $lostItem, FoundItem $foundItem): float
    {
        $score = 0;

        if (($foundItem->category ?? '') === ($lostItem->category ?? '')) {
            $score += 30;
        }

        $foundColors = $this->extractColors((string) ($foundItem->color ?? ''));
        $lostColors = $this->extractColors((string) ($lostItem->color ?? ''));

        $matchedCount = 0;
        foreach ($foundColors as $foundColor) {
            foreach ($lostColors as $lostColor) {
                if ($foundColor !== '' && $lostColor !== '' && str_contains($foundColor, $lostColor)) {
                    $matchedCount++;
                    break;
                }
            }
        }

        if (count($foundColors) > 0 && $matchedCount > 0) {
            $perColor = 25 / count($foundColors);
            $score += min(25, $matchedCount * $perColor);
        }

        if (($foundItem->found_location ?? '') === ($lostItem->lost_location ?? '')) {
            $score += 15;
        }

        $score += $this->calculateBrandScore(
            (string) ($foundItem->brand ?? ''),
            (string) ($lostItem->brand ?? '')
        );

        $score += $this->calculateSerialScore(
            (string) ($foundItem->serial_number ?? ''),
            (string) ($lostItem->serial_number ?? '')
        );

        if ($foundItem->found_time && $lostItem->lost_time) {
            $daysDiff = abs((int) $foundItem->found_time->copy()->startOfDay()
                ->diffInDays($lostItem->lost_time->copy()->startOfDay()));

            if ($daysDiff === 0) {
                $score += 10;
            } elseif ($daysDiff <= 3) {
                $score += 7;
            } elseif ($daysDiff <= 7) {
                $score += 5;
            } else {
                $score += 3;
            }
        }

        if (($foundItem->found_location ?? '') === 'Airplane Cabin' && ($lostItem->lost_location ?? '') === 'Airplane Cabin') {
            $score += $this->calculateFlightScore(
                (string) ($foundItem->flight_number ?? ''),
                (string) ($lostItem->flight_number ?? '')
            );
        }

        $textRelevance = (float) ($foundItem->text_relevance ?? 0);

        if ($textRelevance >= 2.0) {
            $score += 15;
        } elseif ($textRelevance >= 1.0) {
            $score += 12;
        } elseif ($textRelevance >= 0.5) {
            $score += 8;
        } elseif ($textRelevance >= 0.2) {
            $score += 4;
        }

        return min($score, 100);
    }

    private function extractColors(string $colorRaw): array
    {
        $normalized = strtolower($colorRaw);
        $colors = [];

        if (str_contains($normalized, 'multi-color')) {
            if (preg_match('/\((.*?)\)/', $normalized, $matches)) {
                $colors = array_filter(array_map('trim', explode(',', $matches[1])));
            }
        } else {
            if ($normalized !== '') {
                $colors = [trim($normalized)];
            }
        }

        return array_values(array_unique($colors));
    }

    private function calculateBrandScore(string $foundBrand, string $lostBrand): int
    {
        $foundBrandNorm = str_replace([' ', '-'], '', strtoupper($foundBrand));
        $lostBrandNorm = str_replace([' ', '-'], '', strtoupper($lostBrand));

        if ($foundBrandNorm === '' || $lostBrandNorm === '') {
            return 0;
        }

        if ($foundBrandNorm === $lostBrandNorm) {
            return 10;
        }

        if (str_contains($foundBrandNorm, $lostBrandNorm) || str_contains($lostBrandNorm, $foundBrandNorm)) {
            return 5;
        }

        return 0;
    }

    private function calculateSerialScore(string $foundSerial, string $lostSerial): int
    {
        $foundSerialNorm = str_replace([' ', '-'], '', strtoupper($foundSerial));
        $lostSerialNorm = str_replace([' ', '-'], '', strtoupper($lostSerial));

        if ($foundSerialNorm === '' || $lostSerialNorm === '') {
            return 0;
        }

        if ($foundSerialNorm === $lostSerialNorm) {
            return 20;
        }

        if (str_contains($foundSerialNorm, $lostSerialNorm) || str_contains($lostSerialNorm, $foundSerialNorm)) {
            return 10;
        }

        return 0;
    }

    private function calculateFlightScore(string $foundFlight, string $lostFlight): int
    {
        $foundFlightNorm = str_replace([' ', '-'], '', strtoupper($foundFlight));
        $lostFlightNorm = str_replace([' ', '-'], '', strtoupper($lostFlight));

        if ($foundFlightNorm === '' || $lostFlightNorm === '') {
            return 0;
        }

        if ($foundFlightNorm === $lostFlightNorm) {
            return 10;
        }

        if (str_contains($foundFlightNorm, $lostFlightNorm) || str_contains($lostFlightNorm, $foundFlightNorm)) {
            return 5;
        }

        return 0;
    }

    private function getActorName(): string
    {
        return auth()->user()->name
            ?? auth()->user()->username
            ?? 'Staff';
    }

    private function writeActionLog(string $actionType, string $targetName, string $details): void
    {
        AdminActionLog::create([
            'admin_name' => $this->getActorName(),
            'action_type' => $actionType,
            'target_name' => $targetName,
            'details' => $details,
        ]);
    }
}