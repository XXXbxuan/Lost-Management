<?php

namespace App\Services;

use App\Models\FoundItem;
use App\Models\AdminActionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FoundItemService
{
    public function createFoundItem(array $data, $imageFile = null)
    {
        // 1. 获取当前登录的 Staff
        $user = Auth::user();

        // 2. 补全 Staff 信息
        $data['staff_id'] = null; 
        $data['registered_by_name'] = $user ? $user->name : 'Unknown';

        // 3. 处理图片上传
        if ($imageFile) {
            $data['image_path'] = $this->uploadImage($imageFile);
        }

        // 4. 创建记录
        $item = FoundItem::create($data);

        // 5. 写日志 (Audit Log)
        if ($user) {
            AdminActionLog::create([
                'admin_name' => $user->name . ' (Staff)',
                'action_type' => 'REGISTER_FOUND_ITEM',
                'target_name' => "Item #{$item->id}",
                'details' => "Item: {$item->item_name}, Category: {$item->category}, Location: {$item->found_location}"
            ]);
        }

        return $item;
    }

    private function uploadImage($file)
    {
        // 存到 storage/app/public/found_items 文件夹
        return $file->store('found_items', 'public');
    }
}