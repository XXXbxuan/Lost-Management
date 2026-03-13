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
        //get login staff
        $user = Auth::user();

        //staff info
        $data['staff_id'] = null; 
        $data['registered_by_name'] = $user ? $user->name : 'Unknown';

        //process uploaded image
        if ($imageFile) {
            $data['image_path'] = $this->uploadImage($imageFile);
        }

        //create history
        $item = FoundItem::create($data);

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
        return $file->store('found_items', 'public');
    }
}