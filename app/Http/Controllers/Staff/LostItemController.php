<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LostItemReport;
use Illuminate\Http\Request;
use App\Models\FoundItem;
use App\Models\MatchRecord;
use App\Models\AdminActionLog;

class LostItemController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'All');
        $query = LostItemReport::latest();

        if ($status !== 'All') {
            $query->where('status', $status);
        }

        $lostItems = $query->paginate(10);

        return view('staff.lost_reports.index', compact('lostItems', 'status'));
    }

    public function create()
    {
        return view('staff.lost_reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'passenger_name'   => 'required|string|max:255',
            'passenger_email'  => 'required|email|max:255',
            'passenger_phone'  => 'required|string|max:20',
            'item_name'        => 'required|string|max:255',
            'category'         => 'required|string',
            'brand'            => 'nullable|string',
            'serial_number'    => 'nullable|string',
            'color'            => 'required|string',
            'sub_colors'       => 'nullable|array',
            'image'            => 'nullable|image|max:2048',
            'lost_location'    => 'required|string',
            'flight_number'    => 'nullable|string',
            'lost_time'        => 'required|date',
            'description'      => 'nullable|string',
        ]);

        if ($request->has('sub_colors') && is_array($request->input('sub_colors'))) {
            $subColorsString = implode(', ', $request->input('sub_colors'));
            if ($validated['color'] === 'Multi-color') {
                $validated['color'] = 'Multi-color (' . $subColorsString . ')';
            }
        }

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('lost_reports', 'public');
        }

        if (auth()->check()) {
            $validated['staff_id'] = auth()->id();
        }

        $lostItem = LostItemReport::create($validated);

        AdminActionLog::create([
            'admin_name'  => auth()->user()->name ?? 'Staff',
            'action_type' => 'CREATE_LOST_REPORT',
            'target_name' => "Report #{$lostItem->id}",
            'details'     => "Passenger: {$lostItem->passenger_name}, Item: {$lostItem->item_name} ({$lostItem->category})."
        ]);

        return redirect()->route('dashboard')->with('success', '✅ Lost Item Report Submitted Successfully! System will start matching.');
    }

    public function show($id, Request $request)
    {
        $lostItem = LostItemReport::findOrFail($id);

        $rejectedIds = MatchRecord::where('lostId', $id)
            ->where('status', 'Rejected')
            ->pluck('foundId');

        $query = FoundItem::where('status', 'Unclaimed')
            ->whereNotIn('id', $rejectedIds);

        $isManualSearch = $request->has('search');

        if ($isManualSearch) {
            if ($request->filled('category')) $query->where('category', $request->input('category'));
            if ($request->filled('location')) $query->where('found_location', $request->input('location'));
            if ($request->filled('date_from')) $query->whereDate('found_time', '>=', $request->input('date_from'));
            if ($request->filled('date_to')) $query->whereDate('found_time', '<=', $request->input('date_to'));
            if ($request->filled('keyword')) {
                $search = $request->input('keyword');
                $query->where(function ($q) use ($search) {
                    $q->where('item_name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('color', 'LIKE', "%{$search}%");
                });
            }
        } else {
            $query->where('category', $lostItem->category);
            if ($lostItem->lost_time) {
                $query->whereDate('found_time', '>=', $lostItem->lost_time->format('Y-m-d'));
            }
        }

        $candidateMatches = $query->latest()->get();

        foreach ($candidateMatches as $item) {
            $score = 0;

            if ($item->category == $lostItem->category) {
                $score += 40;
            }

            // ✅ Color scoring (30) - Found multi-color is the standard
            // ✅ Color scoring (30) - Found multi-color is the standard (average split)
            // ✅ Color scoring (30) - Found multi-color is the standard (average split)
            $foundColorRaw = strtolower(trim((string) ($item->color ?? '')));
            $lostColorRaw  = strtolower(trim((string) ($lostItem->color ?? '')));

            $parseColors = function ($raw) {
                if ($raw === '') return [];

                // Multi-color (red, blue, yellow)
                if (str_starts_with($raw, 'multi-color')) {
                    if (preg_match('/\((.*?)\)/', $raw, $m)) {
                        $parts = array_map('trim', explode(',', $m[1]));
                        $parts = array_filter($parts, fn($c) => $c !== '');
                        return array_values(array_unique($parts));
                    }
                    return [];
                }

                // Single color
                return [trim($raw)];
            };

            $foundColors = $parseColors($foundColorRaw);
            $lostColors  = $parseColors($lostColorRaw);

            $foundCount = count($foundColors);
            $matchedCount = 0;

            if ($foundCount > 0 && count($lostColors) > 0) {
                // exact match only
                foreach ($foundColors as $fc) {
                    if (in_array($fc, $lostColors, true)) {
                        $matchedCount++;
                    }
                }

                if ($matchedCount > 0) {
                    $perColor = 30 / $foundCount;           // 3 colors => 10 each, 2 colors => 15 each
                    $colorScore = $matchedCount * $perColor;
                    $score += (int) round(min(30, $colorScore));
                }
            }

            if ($item->found_location == $lostItem->lost_location) {
                $score += 20;
            }

            if (str_contains(strtolower($item->item_name), strtolower($lostItem->item_name))) {
                $score += 10;
            }

            $item->similarity_score = min($score, 100);
        }

        if (!$isManualSearch) {
            $candidateMatches = $candidateMatches->filter(fn ($item) => $item->similarity_score >= 50);
        }

        $candidateMatches = $candidateMatches->sortByDesc('similarity_score');
        $rejectedItems = FoundItem::whereIn('id', $rejectedIds)->get();

        return view('staff.lost_reports.show', compact('lostItem', 'candidateMatches', 'rejectedItems'));
    }

    public function verify($lost_id, $found_id, Request $request)
    {
        $lostItem = LostItemReport::findOrFail($lost_id);
        $foundItem = FoundItem::findOrFail($found_id);
        $score = $request->query('score', 0);

        return view('staff.lost_reports.verify', compact('lostItem', 'foundItem', 'score'));
    }

    public function storeMatch(Request $request)
    {
        $request->validate([
            'lost_id'          => 'required',
            'found_id'         => 'required',
            'outcome'          => 'required|in:matched,not_matched',
            'notes'            => 'required|string|max:500',
            'similarity_score' => 'nullable',
            'return_url'       => 'nullable|string',
            'source'           => 'nullable|string',
        ]);

        $score = $request->similarity_score ?? 0;
        $status = $request->outcome === 'matched' ? 'Verified' : 'Rejected';

        MatchRecord::updateOrCreate(
            ['lostId' => $request->lost_id, 'foundId' => $request->found_id],
            [
                'notes'           => $request->notes,
                'status'          => $status,
                'verifiedBy'      => auth()->id(),
                'verifiedAt'      => now(),
                'similarityScore' => $score,
            ]
        );

        $action = $request->outcome === 'matched' ? 'VERIFY_MATCH' : 'REJECT_MATCH';
        if ($request->outcome === 'not_matched' && $request->input('source') === 'reject_claim') {
            $action = 'REJECT_CLAIM';
        }

        AdminActionLog::create([
            'admin_name'  => auth()->user()->name ?? 'Staff',
            'action_type' => $action,
            'target_name' => "Lost #{$request->lost_id} vs Found #{$request->found_id}",
            'details'     => "Result: " . ucfirst($request->outcome) . ", Score: {$score}%. Note: {$request->notes}"
        ]);

        if ($request->outcome === 'matched') {
            LostItemReport::where('id', $request->lost_id)->update(['status' => 'Matched']);
            FoundItem::where('id', $request->found_id)->update(['status' => 'Matched']);
        } else {
            LostItemReport::where('id', $request->lost_id)->update(['status' => 'LOST']);
            $found = FoundItem::find($request->found_id);
            if ($found && $found->status === 'Matched') {
                $found->update(['status' => 'Unclaimed']);
            }
        }

        if (!empty($request->return_url)) {
            return redirect()->to($request->return_url)->with('success', 'Verification record saved.');
        }

        return redirect()->route('staff.lost-items.index')->with('success', 'Verification record saved.');
    }

    public function unmatch($lostId)
    {
        $match = MatchRecord::where('lostId', $lostId)->first();

        if ($match) {
            AdminActionLog::create([
                'admin_name'  => auth()->user()->name ?? 'Staff',
                'action_type' => 'UNDO_MATCH',
                'target_name' => "Match Record #{$match->id}",
                'details'     => "Action: Unlinked Lost Item #{$match->lostId} from Found Item #{$match->foundId}. Status reset."
            ]);

            FoundItem::where('id', $match->foundId)->update(['status' => 'Unclaimed']);
            LostItemReport::where('id', $lostId)->update(['status' => 'LOST']);
            $match->delete();
        }

        return redirect()->back()->with('success', 'Match has been cancelled.');
    }

    public function getTimelineHtml($id)
    {
        $lostItem = LostItemReport::findOrFail($id);
        $match = MatchRecord::where('lostId', $lostItem->id)->where('status', 'Verified')->first();

        if (!$match || !$match->foundItem) {
            return '<div class="p-6 text-center text-gray-500">No verified timeline data available.</div>';
        }

        $foundItem = $match->foundItem;

        return view('staff.claims.partials.timeline', compact('foundItem', 'match'))->render();
    }
}