<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\FoundItem;
use App\Models\User;
use App\Http\Requests\StoreFoundItemRequest;
use App\Services\FoundItemService;
use Illuminate\Http\Request;

class FoundItemController extends Controller
{
    protected $foundItemService;

    public function __construct(FoundItemService $foundItemService)
    {
        $this->foundItemService = $foundItemService;
    }

    public function index(Request $request)
    {
        // 1. 获取当前选中的状态，默认为 'All'
        $status = $request->query('status', 'All');

        // 2. 建立查询
        $query = FoundItem::latest();

        // 3. 如果选的不是 All，就过滤数据库
        if ($status !== 'All') {
            $query->where('status', $status);
        }

        // 4. 获取结果
        $foundItems = $query->paginate(10);

        // 5. 把 $status 传回给页面（为了让按钮高亮）
        return view('staff.found_items.index', compact('foundItems', 'status'));
    }

    public function create()
    {
        return view('staff.found_items.create');
    }

    public function store(StoreFoundItemRequest $request)
    {
        $data = $request->validated();

        // 验证新增的 email 字段
        $request->validate([
            'finder_email' => 'nullable|email'
        ]);

        // =========================================================================
        // 🔥 [新增] 后端强制检查：防止 Slot 重复占用
        // =========================================================================
        
        // 1. 获取用户提交的完整位置 ID (例如 "GEN-S1-01")
        // 注意：前端是通过 <input type="hidden" name="storage_location"> 传过来的
        $targetLocation = $request->input('storage_location'); 

        // 2. 去数据库查：有没有 "位置一样" 且 "还没被领走" 的物品？
        $isOccupied = \App\Models\FoundItem::where('storage_location', $targetLocation)
            ->where('status', '!=', 'Claimed') // 只要不是 Claimed，就算占用
            ->exists();

        // 3. 如果被占用了，直接拦截！返回上一页并报错
        if ($isOccupied) {
            return back()
                ->withInput() // 保留用户刚才填写的 Item Name 等信息，不用重填
                ->withErrors(['storage_location' => "Error: The slot {$targetLocation} is already occupied! Please choose another one."]);
        }
        // =========================================================================


        // 处理 Multi-color 逻辑 (保留你原本的代码)
        if ($request->has('sub_colors') && is_array($request->input('sub_colors'))) {
            $subColorsString = implode(', ', $request->input('sub_colors'));
            // 如果主要颜色选了 Multi-color，就拼接待选颜色
            if ($data['color'] === 'Multi-color') {
                $data['color'] = 'Multi-color (' . $subColorsString . ')';
            }
        }

        // 调用 Service 保存数据
        $this->foundItemService->createFoundItem(
            $data, 
            $request->file('image')
        );

        // =========================================================================
        // 🎁 NEW LOGIC: Award Points to the Finder
        // =========================================================================
        $successMessage = 'Found item registered successfully.';

        if ($request->filled('finder_email')) {
            // Find the user by their email
            $finder = User::where('email', $request->finder_email)->first();

            // If a matching user is found, give them points
            if ($finder) {
                $finder->increment('points', 100); // Give 50 points
                $successMessage = 'Item saved successfully and 50 points were awarded to ' . $finder->name . '!';
            }
        }

        return redirect()->route('staff.found-items.index')
                        ->with('success', $successMessage);
    }
    
    public function checkOccupiedSlots(Request $request)
    {
        $zone = $request->query('zone');  // 例如 "GEN"
        $shelf = $request->query('shelf'); // 例如 "S1"

        // 拼凑出前缀，例如 "GEN-S1-"
        $prefix = $zone . '-' . $shelf . '-';

        // 1. 查询所有以 "GEN-S1-" 开头的 storage_location
        $occupiedItems = \App\Models\FoundItem::where('status', '!=', 'Claimed')
            ->where('storage_location', 'LIKE', $prefix . '%') // 使用 LIKE 查询前缀
            ->get();

        // 2. 从结果中提取出 Slot 号码 (把 "GEN-S1-01" 变成 "01")
        $occupiedSlots = $occupiedItems->map(function ($item) use ($prefix) {
            return str_replace($prefix, '', $item->storage_location);
        })->toArray();

        return response()->json($occupiedSlots);
    }
}