<?php

namespace App\Services;

use App\Models\AdminActionLog;
use App\Models\FoundItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class FoundItemService
{
    public function createFoundItem(array $data, ?UploadedFile $imageFile = null): FoundItem
    {
        $user = Auth::user();

        if (!$user || !$user->staff) {
            throw new RuntimeException('No staff record found for the current logged in user.');
        }

        $data['staff_id'] = $user->staff->staff_id;
        $data['registered_by_name'] = $user->name ?? $user->username ?? 'Unknown';

        if ($imageFile) {
            $data['image_path'] = $this->uploadImage($imageFile);
        }

        $item = FoundItem::create($data);

        $this->writeActionLog(
            $data['registered_by_name'],
            $item
        );

        return $item;
    }

    private function uploadImage(UploadedFile $file): string
    {
        return $file->store('found_items', 'public');
    }

    private function writeActionLog(string $actorName, FoundItem $item): void
    {
        AdminActionLog::create([
            'admin_name' => $actorName,
            'action_type' => 'REGISTER_FOUND_ITEM',
            'target_name' => "Found Item #{$item->id}",
            'details' => "Item: {$item->item_name}, Category: {$item->category}, Location: {$item->found_location}, Storage: {$item->storage_location}",
        ]);
    }
}