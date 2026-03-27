@props(['foundItem' => null])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <label class="block font-bold text-sm text-gray-700">Item Name <span class="text-red-500">*</span></label>
        <input type="text"
               name="item_name"
               value="{{ old('item_name', $foundItem->item_name ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
               required>
    </div>

    <div>
        <label class="block font-bold text-sm text-gray-700">Category <span class="text-red-500">*</span></label>
        <select id="categorySelect"
                name="category"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required>
            @foreach(['Electronics', 'Wallet', 'Identification', 'Baggage', 'Clothing', 'Jewelry', 'Keys', 'Others'] as $cat)
                <option value="{{ $cat }}" {{ old('category', $foundItem->category ?? '') === $cat ? 'selected' : '' }}>
                    {{ $cat }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block font-medium text-sm text-gray-700">Brand (Optional)</label>
        <input type="text"
               name="brand"
               value="{{ old('brand', $foundItem->brand ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="e.g. Apple, Nike">
    </div>

    <div class="md:col-span-2">
        <label class="block font-medium text-sm text-gray-700">Serial Number / Unique ID (Optional)</label>
        <input type="text"
               name="serial_number"
               value="{{ old('serial_number', $foundItem->serial_number ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="e.g. IMEI for phones, Card Number for IDs">
        <p class="text-xs text-gray-500 mt-1">Unique IDs help match items with 100% accuracy.</p>
    </div>

    @php
        $currentColor = old('color', $foundItem->color ?? '');
        $currentSubColors = old('sub_colors', []);

        if (empty($currentSubColors) && !empty($foundItem?->color) && str_starts_with($foundItem->color, 'Multi-color (')) {
            $inside = trim(str_replace(['Multi-color (', ')'], '', $foundItem->color));
            $currentSubColors = array_map('trim', explode(',', $inside));
            $currentColor = 'Multi-color';
        }
    @endphp

    <div class="md:col-span-2">
        <label class="block font-bold text-sm text-gray-700">Color <span class="text-red-500">*</span></label>
        <select id="mainColorSelect"
                name="color"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required>
            <option value="">Select Main Color</option>

            @foreach(['Black','White','Grey','Silver','Gold','Red','Blue','Brown','Green','Purple','Pink','Orange','Yellow'] as $color)
                <option value="{{ $color }}" {{ $currentColor === $color ? 'selected' : '' }}>
                    {{ $color }}
                </option>
            @endforeach

            <option value="Multi-color" {{ $currentColor === 'Multi-color' ? 'selected' : '' }}>
                🎨 Multi-color (Select details below)
            </option>
        </select>

        <div id="multiColorOptions" class="{{ $currentColor === 'Multi-color' ? '' : 'hidden' }} mt-3 p-3 bg-gray-50 border border-gray-200 rounded-md">
            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Please tick all colors involved:</label>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach(['Black','White','Red','Blue','Green','Yellow','Orange','Purple','Pink','Grey','Brown','Silver','Gold'] as $subColor)
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="sub_colors[]"
                               value="{{ $subColor }}"
                               class="rounded text-indigo-600 focus:ring-indigo-500"
                               {{ in_array($subColor, $currentSubColors) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">{{ $subColor }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="md:col-span-2">
        <label class="block font-bold text-sm text-gray-700">Photo (Optional)</label>
        <input type="file"
               name="image"
               class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-50">

        @if(!empty($foundItem?->image_path))
            <div class="mt-3">
                <p class="text-xs font-medium text-gray-500 mb-2">Current Image</p>
                <img src="{{ asset('storage/' . $foundItem->image_path) }}"
                     class="w-32 h-32 object-cover rounded border shadow-sm">
            </div>
        @endif
    </div>
</div>