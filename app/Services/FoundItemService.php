<?php

namespace App\Models; // 或是 App\Services; 依據你的檔案位置

namespace App\Services;

use App\Models\FoundItem;
use App\Models\AdminActionLog;
use Illuminate\Support\Facades\Auth;

class FoundItemService
{
    public function createFoundItem(array $data, $imageFile = null)
    {
        // 1. 獲取當前登入的 User（Admin/Staff）
        $user = Auth::user();

        // 2. ✅ 補全 Staff 資訊（你的版本：確保資料庫能追蹤到是誰登記的）
        // 邏輯：User hasOne Staff，staff 表主鍵是 staff_id
        if ($user && $user->staff) {
            $data['staff_id'] = $user->staff->staff_id;
        } else {
            // fallback：極端情況下至少記錄 User ID
            $data['staff_id'] = $user?->id;
        }

        // 3. 註冊名稱：優先取 username，其次 name，最後 Unknown
        $data['registered_by_name'] = $user?->username ?? $user?->name ?? 'Unknown';

        // 4. 處理圖片上傳
        if ($imageFile) {
            $data['image_path'] = $this->uploadImage($imageFile);
        }

        // 5. 建立拾獲紀錄
        $item = FoundItem::create($data);

        // 6. 寫入審計日誌（AdminActionLog）
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
        return $file->store('found_items', 'public');
    }
}