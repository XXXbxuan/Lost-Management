<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\FoundItem;
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

    public function index()
    {
        // 显示所有 Found Items
        $foundItems = FoundItem::latest()->paginate(10);
        return view('staff.found_items.index', compact('foundItems'));
    }

    public function create()
    {
        return view('staff.found_items.create');
    }

    public function store(StoreFoundItemRequest $request)
    {
        $data = $request->validated();

        // 处理 Multi-color 逻辑
        if ($request->has('sub_colors') && is_array($request->input('sub_colors'))) {
            $subColorsString = implode(', ', $request->input('sub_colors'));
            // 如果主要颜色选了 Multi-color，就拼接待选颜色
            if ($data['color'] === 'Multi-color') {
                $data['color'] = 'Multi-color (' . $subColorsString . ')';
            }
        }

        $this->foundItemService->createFoundItem(
            $data, 
            $request->file('image')
        );

        return redirect()->route('staff.found-items.index')
                         ->with('success', 'Found item registered successfully.');
    }
}