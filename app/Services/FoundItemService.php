<?php

namespace App\Services;

use App\Models\FoundItem;
use App\Models\AdminActionLog;
use Illuminate\Support\Facades\Auth;

class FoundItemService
{
    public function createFoundItem(array $data, $imageFile = null)
    {
        // 1. Get current logged in user
        $user = Auth::user();

        // 2. Ensure current user has linked staff record
        if (!$user || !$user->staff) {
            throw new \Exception('No staff record found for the current logged in user.');
        }

        // found_items.staff_id must reference staff.staff_id
        $data['staff_id'] = $user->staff->staff_id;

        // 3. Registered by name
        $data['registered_by_name'] = $user->username ?? $user->name ?? 'Unknown';

        // 4. Handle image upload
        if ($imageFile) {
            $data['image_path'] = $this->uploadImage($imageFile);
        }

        // 5. Create found item record
        $item = FoundItem::create($data);

        // 6. Write audit log
        AdminActionLog::create([
            'admin_name'  => $data['registered_by_name'],
            'action_type' => 'REGISTER_FOUND_ITEM',
            'target_name' => "Found Item #{$item->id}",
            'details'     => "Item: {$item->item_name}, Category: {$item->category}, Location: {$item->found_location}, Storage: {$item->storage_location}"
        ]);

        return $item;
    }

    private function uploadImage($file)
    {
        return $file->store('found_items', 'public');
    }
}