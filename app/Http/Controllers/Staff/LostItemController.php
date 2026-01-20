<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LostItem;
use App\Services\LostItemService;
use App\Http\Requests\StoreLostItemRequest;
use Illuminate\Http\Request;

class LostItemController extends Controller
{
    protected $lostItemService;

    public function __construct(LostItemService $lostItemService)
    {
        $this->lostItemService = $lostItemService;
    }

    /**
     * 1. 列表页 (Index)
     */
    public function index(Request $request)
    {
        $query = LostItem::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('found_location', 'like', "%{$search}%");
            });
        }

        $lostItems = $query->latest('found_time')->paginate(10);

        // 这里必须返回 View，之前可能漏了这句
        return view('staff.lost_items.index', compact('lostItems'));
    }

    /**
     * 2. 创建页 (Create)
     */
    public function create()
    {
        return view('staff.lost_items.create');
    }

    /**
     * 3. 保存逻辑 (Store)
     */
    public function store(StoreLostItemRequest $request)
    {
        // 获取所有验证过的数据
        $data = $request->validated();

        // 【核心修改】处理颜色逻辑
        // 如果用户在 "Multi-color" 模式下选了具体颜色 (colors 数组)，我们就把它拼成字符串
        if ($request->has('sub_colors') && is_array($request->input('sub_colors'))) {
            // 结果会变成: "Multi-color (Red, Black, White)"
            $subColorsString = implode(', ', $request->input('sub_colors'));
            $data['color'] = 'Multi-color (' . $subColorsString . ')';
        }

        // 调用 Service 创建物品
        $this->lostItemService->createLostItem(
            $data, 
            $request->file('image')
        );

        return redirect()->route('staff.lost-items.index')
                         ->with('success', 'Lost item registered successfully.');
    }
}