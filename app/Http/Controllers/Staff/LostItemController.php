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

        // 🌟 已統一為 $lostItems
        $lostItems = $query->paginate(10);

        // 🌟 傳給 View 的變數改為 'lostItems'
        return view('staff.lost_reports.index', compact('lostItems', 'status'));
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

        // 🌟 核心修正：在這裡偷偷把當前登入的 Staff ID 塞進去
        // 這樣資料庫就會記下「是這位員工幫忙建立這筆報失單的」
        if (auth()->check()) {
            $validated['staff_id'] = auth()->id();
        }

        // 🌟 已統一為 $lostItem，現在它會帶著 staff_id 一起存進去！
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
        // 🌟 已統一為 $lostItem
        $lostItem = LostItemReport::findOrFail($id);

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
            // 🌟 這裡面的變數全部改為 $lostItem
            $query->where('category', $lostItem->category);
            if ($lostItem->lost_time) {
                 $query->whereDate('found_time', '>=', $lostItem->lost_time->format('Y-m-d'));
            }
        }

        $candidateMatches = $query->latest()->get();

        foreach ($candidateMatches as $item) {
            $score = 0;
            // 🌟 這裡面的變數全部改為 $lostItem
            if ($item->category == $lostItem->category) $score += 40;
            
            if (str_contains(strtolower($item->color), strtolower($lostItem->color)) || 
                str_contains(strtolower($lostItem->color), strtolower($item->color))) {
                $score += 30;
            }
            
            if ($item->found_location == $lostItem->lost_location) $score += 20;
            
            if (str_contains(strtolower($item->item_name), strtolower($lostItem->item_name))) $score += 10;

            $item->similarity_score = min($score, 100);
        }

        if (!$isManualSearch) {
            $candidateMatches = $candidateMatches->filter(function ($item) {
                return $item->similarity_score >= 50; 
            });
        }
        
        $candidateMatches = $candidateMatches->sortByDesc('similarity_score');
        $rejectedItems = FoundItem::whereIn('id', $rejectedIds)->get();

        // 🌟 傳給 View 的變數改為 'lostItem'
        return view('staff.lost_reports.show', compact('lostItem', 'candidateMatches', 'rejectedItems'));
    }

    public function verify($lost_id, $found_id, Request $request)
    {
        // 🌟 已統一為 $lostItem
        $lostItem = LostItemReport::findOrFail($lost_id);
        $foundItem = FoundItem::findOrFail($found_id);
        $score = $request->query('score', 0); 
        
        // 🌟 傳給 View 的變數改為 'lostItem'
        return view('staff.lost_reports.verify', compact('lostItem', 'foundItem', 'score'));
    }

    public function storeMatch(Request $request)
    {
        $request->validate([
            'lost_id' => 'required',
            'found_id' => 'required',
            'outcome' => 'required|in:matched,not_matched',
            'notes' => 'required|string|max:500',
            'similarity_score' => 'nullable',
            'return_url' => 'nullable|string',
            'source' => 'nullable|string',
        ]);

        $score = $request->similarity_score;
        if ($request->outcome === 'not_matched' && ($score === null || $score === '')) {
            $score = 0;
        }

        $status = $request->outcome === 'matched' ? 'Verified' : 'Rejected';

        MatchRecord::updateOrCreate(
            ['lostId' => $request->lost_id, 'foundId' => $request->found_id],
            [
                'notes' => $request->notes,
                'status' => $status,
                'verifiedBy' => auth()->id(),
                'verifiedAt' => now(),
                'similarityScore' => $score,
            ]
        );

        // ✅ Action type：Reject Claim 显示 REJECT_CLAIM
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
            // ✅ matched：两边标记 Matched
            LostItemReport::where('id', $request->lost_id)->update(['status' => 'Matched']);
            FoundItem::where('id', $request->found_id)->update(['status' => 'Matched']);
        } else {
            // ✅ not_matched：Lost 回到 LOST
            LostItemReport::where('id', $request->lost_id)->update(['status' => 'LOST']);

            // ✅ not_matched：Found 释放回 Unclaimed（只在目前是 Matched 时才退回）
            $found = FoundItem::find($request->found_id);
            if ($found && $found->status === 'Matched') {
                $found->update(['status' => 'Unclaimed']);
            }
        }

        $returnUrl = $request->input('return_url');
        if (!empty($returnUrl)) {
            return redirect()->to($returnUrl)->with('success', 'Verification record saved.');
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

        return redirect()->back()->with('success', 'Match has been cancelled. Items are back to original status.');
    }

    public function getTimelineHtml($id)
    {
        // 🌟 已統一為 $lostItem
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
}