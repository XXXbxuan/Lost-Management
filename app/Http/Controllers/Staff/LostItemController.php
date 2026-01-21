<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LostItemReport; // 依然使用 Report 模型存数据，逻辑更通顺
use Illuminate\Http\Request;
use App\Models\FoundItem;       // <--- 🔥 报错就是因为缺了这一行！

class LostItemController extends Controller
{
    public function index()
    {
        // 获取所有报失单，最新的在最前面，每页显示 10 条
        $lostReports = LostItemReport::latest()->paginate(10);
        
        return view('staff.lost_reports.index', compact('lostReports'));
    }
    // 1. 显示报失表单 (乘客端)
    public function create()
    {
        // 这里的 View 我们等下建立，名字叫 lost_reports 也没问题
        return view('staff.lost_reports.create');
    }

    // 2. 保存报失数据
    public function store(Request $request)
    {
        // 验证规则：乘客信息 + 物品详情 + 丢失地点
        $validated = $request->validate([
            // --- 乘客联系方式 ---
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => 'required|string|max:20',
            
            // --- 物品详情 (复用组件的字段) ---
            'item_name' => 'required|string|max:255',
            'category' => 'required|string',
            'brand' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'color' => 'required|string',
            'sub_colors' => 'nullable|array', // 多色逻辑
            'image' => 'nullable|image|max:2048',

            // --- 丢失时间和地点 ---
            'lost_location' => 'required|string', // 比如 Gate 5
            'flight_number' => 'nullable|string', 
            'lost_time' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // --- 逻辑处理：如果有选多色，就把数组变成字符串 ---
        if ($request->has('sub_colors') && is_array($request->input('sub_colors'))) {
            $subColorsString = implode(', ', $request->input('sub_colors'));
            if ($validated['color'] === 'Multi-color') {
                $validated['color'] = 'Multi-color (' . $subColorsString . ')';
            }
        }
        
        // --- 逻辑处理：上传图片 ---
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('lost_reports', 'public');
        }

        // --- 存入数据库 ---
        LostItemReport::create($validated);

        return redirect()->route('dashboard')->with('success', '✅ Lost Item Report Submitted Successfully! System will start matching.');
    }

    public function show($id, Request $request)
    {
        // 1. 获取当前的主角：报失单
        $lostReport = LostItemReport::findOrFail($id);

        // 2. 初始化查询构建器
        $query = FoundItem::where('status', 'Unclaimed');

        $isManualSearch = $request->has('search');

        if ($isManualSearch) {
            // === Manual Refinement ===
            if ($request->filled('category') && $request->input('category') != '') {
                $query->where('category', $request->input('category'));
            }
            
            // 🔥 [修复] 把 'location' 改为 'found_location'
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
            // === Automated Init ===
            $query->where('category', $lostReport->category);
            
            if ($lostReport->lost_time) {
                 $query->whereDate('found_time', '>=', $lostReport->lost_time->format('Y-m-d'));
            }
        }

        // 3. 执行查询
        $candidateMatches = $query->latest()->get();

        // 4. 计算分数
        foreach ($candidateMatches as $item) {
            $score = 0;
            // A: Category
            if ($item->category == $lostReport->category) $score += 40;
            
            // B: Color
            if (str_contains(strtolower($item->color), strtolower($lostReport->color)) || 
                str_contains(strtolower($lostReport->color), strtolower($item->color))) {
                $score += 30;
            }
            
            // C: Location - 🔥 [修复] 这里也改成 found_location
            // 只有当“拾获地点”和“丢失地点”完全一样时才加分
            if ($item->found_location == $lostReport->lost_location) $score += 20;

            // D: Keyword
            if (str_contains(strtolower($item->item_name), strtolower($lostReport->item_name))) $score += 10;

            $item->similarity_score = min($score, 100);
        }

        // 只有在自动模式下才过滤低分，手动模式不过滤
        if (!$isManualSearch) {
            $candidateMatches = $candidateMatches->filter(function ($item) {
                return $item->similarity_score >= 50; 
            });
        }
        
        $candidateMatches = $candidateMatches->sortByDesc('similarity_score');

        return view('staff.lost_reports.show', compact('lostReport', 'candidateMatches'));
    }
    public function verify($lost_id, $found_id)
    {
        // 获取两个主角
        $lostReport = LostItemReport::findOrFail($lost_id);
        $foundItem = FoundItem::findOrFail($found_id);

        // 带他们去“相亲房” (视图)
        return view('staff.lost_reports.verify', compact('lostReport', 'foundItem'));
    }
}