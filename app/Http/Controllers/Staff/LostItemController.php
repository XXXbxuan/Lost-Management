<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LostItemReport;
use Illuminate\Http\Request;
use App\Models\FoundItem;
use App\Models\MatchRecord;
use App\Models\AdminActionLog;
use Illuminate\Support\Facades\DB;

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
            'brand'            => 'nullable|string|max:255',
            'serial_number'    => 'nullable|string|max:255',
            'color'            => 'required|string|max:255',
            'sub_colors'       => 'nullable|array',
            'sub_colors.*'     => 'string|max:50',
            'image'            => 'nullable|image|max:2048',
            'lost_location'    => 'required|string|max:255',
            'flight_number'    => 'nullable|string|max:50',
            'lost_time'        => 'required|date',
            'description'      => 'nullable|string',
        ]);

        // ✅ Multi-color 存成 "Multi-color (Red, Blue, ...)"
        if ($request->filled('sub_colors') && is_array($request->input('sub_colors'))) {
            $subColors = array_filter(array_map('trim', $request->input('sub_colors')));

            if (($validated['color'] ?? '') === 'Multi-color' && count($subColors) > 0) {
                $validated['color'] = 'Multi-color (' . implode(', ', $subColors) . ')';
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

        return redirect()->route('dashboard')
            ->with('success', '✅ Lost Item Report Submitted Successfully! System will start matching.');
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

        // ✅ FULLTEXT query (你已經在 found_items 建好 FULLTEXT index: item_name, brand, description)
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
            $score = 0;

            // ----------------------------
            // 1) Category (30)
            // ----------------------------
            if (($item->category ?? '') === ($lostItem->category ?? '')) {
                $score += 30;
            }

            // ----------------------------
            // 2) Color (25) - Found multi-color is the standard (avg split)
            // ----------------------------
            $foundColorRaw = strtolower((string) ($item->color ?? ''));
            $lostColorRaw  = strtolower((string) ($lostItem->color ?? ''));

            // Parse Found colors
            $foundColors = [];
            if (str_contains($foundColorRaw, 'multi-color')) {
                if (preg_match('/\((.*?)\)/', $foundColorRaw, $m)) {
                    $foundColors = array_filter(array_map('trim', explode(',', $m[1])));
                }
            } else {
                if ($foundColorRaw !== '') $foundColors = [trim($foundColorRaw)];
            }

            // Parse Lost colors
            $lostColors = [];
            if (str_contains($lostColorRaw, 'multi-color')) {
                if (preg_match('/\((.*?)\)/', $lostColorRaw, $m2)) {
                    $lostColors = array_filter(array_map('trim', explode(',', $m2[1])));
                }
            } else {
                if ($lostColorRaw !== '') $lostColors = [trim($lostColorRaw)];
            }

            $foundColors = array_values(array_unique($foundColors));
            $lostColors  = array_values(array_unique($lostColors));

            $matchedCount = 0;
            foreach ($foundColors as $fc) {
                foreach ($lostColors as $lc) {
                    if ($fc !== '' && $lc !== '' && str_contains($fc, $lc)) {
                        $matchedCount++;
                        break;
                    }
                }
            }

            if (count($foundColors) > 0 && $matchedCount > 0) {
                $perColor = 25 / count($foundColors);
                $score += min(25, $matchedCount * $perColor);
            }

            // ----------------------------
            // 3) Location (15)
            // ----------------------------
            if (($item->found_location ?? '') === ($lostItem->lost_location ?? '')) {
                $score += 15;
            }

            // ----------------------------
            // 4) Brand (10)
            // ----------------------------
            $foundBrand = strtoupper((string) ($item->brand ?? ''));
            $lostBrand  = strtoupper((string) ($lostItem->brand ?? ''));

            $foundBrandNorm = str_replace([' ', '-'], '', $foundBrand);
            $lostBrandNorm  = str_replace([' ', '-'], '', $lostBrand);

            if ($foundBrandNorm !== '' && $lostBrandNorm !== '') {
                if ($foundBrandNorm === $lostBrandNorm) {
                    $score += 10;
                } else if (str_contains($foundBrandNorm, $lostBrandNorm) || str_contains($lostBrandNorm, $foundBrandNorm)) {
                    $score += 5;
                }
            }

            // ----------------------------
            // 5) Serial Number (20) - exact 20, partial 10
            // ----------------------------
            $foundSerial = strtoupper((string) ($item->serial_number ?? ''));
            $lostSerial  = strtoupper((string) ($lostItem->serial_number ?? ''));

            $foundSerialNorm = str_replace([' ', '-'], '', $foundSerial);
            $lostSerialNorm  = str_replace([' ', '-'], '', $lostSerial);

            if ($foundSerialNorm !== '' && $lostSerialNorm !== '') {
                if ($foundSerialNorm === $lostSerialNorm) {
                    $score += 20;
                } else if (str_contains($foundSerialNorm, $lostSerialNorm) || str_contains($lostSerialNorm, $foundSerialNorm)) {
                    $score += 10;
                }
            }

            // ----------------------------
            // 6) Time difference (10)
            // ----------------------------
            if ($item->found_time && $lostItem->lost_time) {
                $daysDiff = abs((int) $item->found_time->copy()->startOfDay()
                    ->diffInDays($lostItem->lost_time->copy()->startOfDay()));

                if ($daysDiff == 0) $score += 10;
                else if ($daysDiff <= 3) $score += 7;
                else if ($daysDiff <= 7) $score += 5;
                else $score += 3;
            }

            // ----------------------------
            // 7) Flight number (10) - only if BOTH locations are Airplane Cabin
            // ----------------------------
            if (($item->found_location ?? '') === 'Airplane Cabin' && ($lostItem->lost_location ?? '') === 'Airplane Cabin') {
                $foundFlight = strtoupper((string) ($item->flight_number ?? ''));
                $lostFlight  = strtoupper((string) ($lostItem->flight_number ?? ''));

                $foundFlightNorm = str_replace([' ', '-'], '', $foundFlight);
                $lostFlightNorm  = str_replace([' ', '-'], '', $lostFlight);

                if ($foundFlightNorm !== '' && $lostFlightNorm !== '') {
                    if ($foundFlightNorm === $lostFlightNorm) {
                        $score += 10;
                    } else if (str_contains($foundFlightNorm, $lostFlightNorm) || str_contains($lostFlightNorm, $foundFlightNorm)) {
                        $score += 5;
                    }
                }
            }

            // ----------------------------
            // 8) Text match (15) - FULLTEXT result (只加分，不控制排序)
            // ----------------------------
            $textRel = (float) ($item->text_relevance ?? 0);
            if ($textRel >= 2.0) $score += 15;
            else if ($textRel >= 1.0) $score += 12;
            else if ($textRel >= 0.5) $score += 8;
            else if ($textRel >= 0.2) $score += 4;

            // ✅ 分數可以超過100沒關係，但顯示最多100%
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