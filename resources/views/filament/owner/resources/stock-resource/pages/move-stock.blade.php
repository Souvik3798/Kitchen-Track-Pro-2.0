<x-filament-panels::page>
    <form wire:submit.prevent="moveStock" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Inventory Field -->
            <div>
                <label for="inventory_name" class="block text-sm font-medium text-gray-700">Inventory</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="text" id="inventory_name"
                        class="form-input block w-full pr-12 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        wire:model.defer="inventory_name" disabled>
                </div>
            </div>

            <!-- Store Quantity Field -->
            <div>
                <label for="store_quantity" class="block text-sm font-medium text-gray-700">Quantity in Store</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="number" id="store_quantity"
                        class="form-input block w-full pr-16 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        wire:model.defer="store_quantity" disabled>

                </div>
            </div>
        </div>

        <!-- Quantity to Move Field -->
        <div>
            <label for="quantity_to_move" class="block text-sm font-medium text-gray-700">Quantity to Move to
                Kitchen</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="number" id="quantity_to_move"
                    class="form-input block w-2 pr-16 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    wire:model.defer="quantity_to_move" required>

            </div>
        </div>

        <!-- Move Stock Button -->
        <div class="pt-4">
            <button type="submit"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                style="background-color: #6736c4; color: white;">
                Move Stock
            </button>
        </div>
    </form>
</x-filament-panels::page>
