<?php

namespace App\Services;

use App\Models\FoundItem;
use App\Models\AdminActionLog;
use Illuminate\Support\Facades\Auth;

class FoundItemService
{
    public function createFoundItem(array $data, $imageFile = null)
    {
        // 1. 获取当前登录的 User（Admin/Staff）
        $user = Auth::user();

        // 2. ✅ 补全 Staff 信息（不要再覆盖成 null）
        //    你系统：User hasOne Staff(profile)，staff 表主键是 staff_id
        if ($user && $user->staff) {
            $data['staff_id'] = $user->staff->staff_id;
        } else {
            // fallback：至少不要 NULL（极端情况下）
            $data['staff_id'] = $user?->id;
        }

        // registered_by_name：优先 username，其次 name
        $data['registered_by_name'] = $user?->username ?? $user?->name ?? 'Unknown';

        // 3. 处理图片上传
        if ($imageFile) {
            $data['image_path'] = $this->uploadImage($imageFile);
        }

        // 4. 创建记录
        $item = FoundItem::create($data);

        // 5. 写日志 (Audit Log)
        if ($user) {
            AdminActionLog::create([
                'admin_name'  => $data['registered_by_name'],
                'action_type' => 'REGISTER_FOUND_ITEM',
                'target_name' => "Found Item #{$item->id}",
                'details'     => "Item: {$item->item_name}, Category: {$item->category}, Location: {$item->found_location}, Storage: {$item->storage_location}"
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