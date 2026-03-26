<?php

namespace Database\Seeders;

use App\Models\StorageSlot;
use Illuminate\Database\Seeder;

class StorageSlotSeeder extends Seeder
{
    public function run(): void
    {
        $zones = ['GEN', 'VAULT', 'BAG'];
        $shelves = ['S1', 'S2', 'S3'];

        foreach ($zones as $zone) {
            foreach ($shelves as $shelf) {
                for ($i = 1; $i <= 10; $i++) {
                    $slotCode = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $fullCode = $zone . '-' . $shelf . '-' . $slotCode;

                    StorageSlot::firstOrCreate(
                        ['full_code' => $fullCode],
                        [
                            'zone_code' => $zone,
                            'shelf_code' => $shelf,
                            'slot_code' => $slotCode,
                            'slot_status' => 'Available',
                            'remark' => null,
                        ]
                    );
                }
            }
        }
    }
}