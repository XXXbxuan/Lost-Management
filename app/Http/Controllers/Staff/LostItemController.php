<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LostItemReport; // 依然使用 Report 模型存数据，逻辑更通顺
use Illuminate\Http\Request;

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
}