<?php

namespace App\Services;

use App\Models\LostItem;
use App\Models\AdminActionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LostItemService
{
    /**
     * 核心功能：登记新物品
     */
    public function createLostItem(array $data, $imageFile = null)
    {
        // 1. 准备基础数据
        $user = Auth::user();
        
        // [Traceability] 存死数据（名字）
        $data['staff_id'] = $user->staff->staff_id ?? null; 
        $data['registered_by_name'] = $user->name;          
        $data['found_time'] = $data['found_time'] ?? now(); 

        // 2. 处理图片上传
        if ($imageFile) {
            $data['image_path'] = $this->uploadImage($imageFile);
        }

        // 3. 创建记录
        $item = LostItem::create($data);

        // 4. [Audit Log] 写日志
        AdminActionLog::create([
            'admin_name'  => $user->name . ' (Staff)',
            'action_type' => 'REGISTER_ITEM',
            'target_name' => "Item #{$item->id}",
            'details'     => "Item: {$item->item_name}, Category: {$item->category}, Location: {$item->found_location}"
        ]);

        return $item;
    }

    /**
     * 辅助功能：图片上传处理
     */
    private function uploadImage($file)
    {
        // 重命名防止冲突
        $filename = 'lost_item_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('lost_items', $filename, 'public');
    }
}