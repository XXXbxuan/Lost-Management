<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <label class="block font-bold text-sm text-gray-700">Item Name <span class="text-red-500">*</span></label>
        <input type="text" name="item_name" value="{{ old('item_name') }}" 
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
    </div>

    <div>
        <label class="block font-bold text-sm text-gray-700">Category <span class="text-red-500">*</span></label>
        <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @foreach(['Electronics', 'Wallet', 'Identification', 'Baggage', 'Clothing', 'Jewelry', 'Keys', 'Others'] as $cat)
                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block font-medium text-sm text-gray-700">Brand (Optional)</label>
        <input type="text" name="brand" value="{{ old('brand') }}" 
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Apple, Nike">
    </div>

    <div class="md:col-span-2">
        <label class="block font-medium text-sm text-gray-700">Serial Number / Unique ID (Optional)</label>
        <input type="text" name="serial_number" value="{{ old('serial_number') }}" 
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. IMEI for phones, Card Number for IDs">
        <p class="text-xs text-gray-500 mt-1">Unique IDs help match items with 100% accuracy.</p>
    </div>

    <div class="md:col-span-2">
        <label class="block font-bold text-sm text-gray-700">Color <span class="text-red-500">*</span></label>
        <select name="color" id="mainColorSelect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="toggleMultiColor()">
            <option value="">Select Main Color</option>
            @foreach(['Black', 'White', 'Grey', 'Silver', 'Gold', 'Red', 'Blue', 'Brown', 'Green', 'Purple', 'Pink', 'Orange', 'Yellow'] as $color)
                <option value="{{ $color }}" {{ old('color') == $color ? 'selected' : '' }}>{{ $color }}</option>
            @endforeach
            <option value="Multi-color" {{ old('color') == 'Multi-color' ? 'selected' : '' }}>🎨 Multi-color (Select details below)</option>
            <option value="Others">Others</option>
        </select>

        <div id="multiColorOptions" class="hidden mt-3 p-3 bg-gray-50 border border-gray-200 rounded-md">
            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Please tick all colors involved:</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach(['Black', 'White', 'Red', 'Blue', 'Green', 'Yellow', 'Orange', 'Purple', 'Pink', 'Grey', 'Brown', 'Silver', 'Gold'] as $subColor)
                    <div class="flex items-center">
                        <input type="checkbox" name="sub_colors[]" value="{{ $subColor }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $subColor }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="md:col-span-2">
        <label class="block font-bold text-sm text-gray-700">Photo (Optional)</label>
        <input type="file" name="image" class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-50">
    </div>
</div>