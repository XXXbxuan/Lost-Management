<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LostItemReport;
use Illuminate\Http\Request;
use App\Models\FoundItem;
use App\Models\MatchRecord;
// ❌ 删除了 use Auth;

class LostItemController extends Controller
{
    public function index()
    {
        $lostReports = LostItemReport::latest()->paginate(10);
        return view('staff.lost_reports.index', compact('lostReports'));
    }

    public function create()
    {
        return view('staff.lost_reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => 'required|string|max:20',
            'item_name' => 'required|string|max:255',
            'category' => 'required|string',
            'brand' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'color' => 'required|string',
            'sub_colors' => 'nullable|array',
            'image' => 'nullable|image|max:2048',
            'lost_location' => 'required|string',
            'flight_number' => 'nullable|string', 
            'lost_time' => 'required|date',
            'description' => 'nullable|string',
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

        LostItemReport::create($validated);

        return redirect()->route('dashboard')->with('success', '✅ Lost Item Report Submitted Successfully! System will start matching.');
    }

    public function show($id, Request $request)
    {
        $lostReport = LostItemReport::findOrFail($id);

        $rejectedIds = MatchRecord::where('lostId', $id)
            ->where('status', 'Rejected')
            ->pluck('foundId');

        $query = FoundItem::where('status', 'Unclaimed')
            ->whereNotIn('id', $rejectedIds);

        $isManualSearch = $request->has('search');

        if ($isManualSearch) {
            if ($request->filled('category') && $request->input('category') != '') {
                $query->where('category', $request->input('category'));
            }
            if ($request->filled('location') && $request->input('location') != '') {
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
                $query->where(function($q) use ($search) {
                    $q->where('item_name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhere('color', 'LIKE', "%{$search}%");
                });
            }
        } else {
            $query->where('category', $lostReport->category);
            if ($lostReport->lost_time) {
                 $query->whereDate('found_time', '>=', $lostReport->lost_time->format('Y-m-d'));
            }
        }

        $candidateMatches = $query->latest()->get();

        foreach ($candidateMatches as $item) {
            $score = 0;
            // A: Category
            if ($item->category == $lostReport->category) $score += 40;
            
            // B: Color
            if (str_contains(strtolower($item->color), strtolower($lostReport->color)) || 
                str_contains(strtolower($lostReport->color), strtolower($item->color))) {
                $score += 30;
            }
            
            // C: Location
            if ($item->found_location == $lostReport->lost_location) $score += 20;

            // D: Keyword
            if (str_contains(strtolower($item->item_name), strtolower($lostReport->item_name))) $score += 10;

            $item->similarity_score = min($score, 100);
        }

        if (!$isManualSearch) {
            $candidateMatches = $candidateMatches->filter(function ($item) {
                return $item->similarity_score >= 50; 
            });
        }
        
        $candidateMatches = $candidateMatches->sortByDesc('similarity_score');
        $rejectedItems = FoundItem::whereIn('id', $rejectedIds)->get();

        return view('staff.lost_reports.show', compact('lostReport', 'candidateMatches', 'rejectedItems'));
    }

    public function verify($lost_id, $found_id, Request $request)
    {
        $lostReport = LostItemReport::findOrFail($lost_id);
        $foundItem = FoundItem::findOrFail($found_id);

        $score = $request->query('score', 0); 

        return view('staff.lost_reports.verify', compact('lostReport', 'foundItem', 'score'));
    }

    public function storeMatch(Request $request)
    {
        $request->validate([
            'lost_id' => 'required',
            'found_id' => 'required',
            'outcome' => 'required|in:matched,not_matched',
            'notes' => 'required|string|max:500',
            'similarity_score' => 'required', 
        ]);

        MatchRecord::create([
            'lostId' => $request->lost_id,
            'foundId' => $request->found_id,
            'notes' => $request->notes,
            'status' => $request->outcome == 'matched' ? 'Verified' : 'Rejected',
            
            // ✅ 改回 auth()->id()
            'verifiedBy' => auth()->id(),
            
            'verifiedAt' => now(),
            'similarityScore' => $request->similarity_score, 
        ]);

        if ($request->outcome == 'matched') {
            LostItemReport::where('id', $request->lost_id)->update(['status' => 'Matched']);
            FoundItem::where('id', $request->found_id)->update(['status' => 'Matched']);
        }

        return redirect()->route('staff.lost-items.index')->with('success', 'Verification record saved.');
    }

    public function unmatch($lostId)
    {
        $match = MatchRecord::where('lostId', $lostId)->first();
        
        if ($match) {
            FoundItem::where('id', $match->foundId)->update(['status' => 'Unclaimed']);
            LostItemReport::where('id', $lostId)->update(['status' => 'LOST']);
            $match->delete();
        }

        return redirect()->back()->with('success', 'Match has been cancelled. Items are back to original status.');
    }

    public function getTimelineHtml($id)
    {
        $lostReport = LostItemReport::findOrFail($id);
        
        $match = MatchRecord::where('lostId', $lostReport->id)
                    ->where('status', 'Verified')
                    ->first();

        if (!$match || !$match->foundItem) {
            return '<div class="p-6 text-center text-gray-500">No verified timeline data available.</div>';
        }

        $foundItem = $match->foundItem;

        return view('staff.claims.partials.timeline', compact('foundItem', 'match'))->render();
    }
}