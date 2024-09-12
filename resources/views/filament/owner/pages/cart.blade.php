<x-filament-panels::page>
    <!-- Container for the entire cart page -->
    <div style="max-width: 1000px; margin: 0 auto; padding: 20px;">
        <!-- Page Title -->
        <h2 style="text-align: center; font-size: 24px; font-weight: bold; color: #333;">
            <i class="fas fa-shopping-cart" style="color: #6B46C1; margin-right: 10px;"></i>Your Cart
        </h2>

        <!-- Empty Cart Message -->
        @if ($cartItems->isEmpty())
            <div style="text-align: center; color: #666; margin-top: 50px;">
                <i class="fas fa-box-open" style="color: #E2E8F0; font-size: 80px; margin-bottom: 20px;"></i>
                <p>Your cart is empty.</p>
            </div>
        @else
            <!-- List of Cart Items -->
            <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 30px;">
                @foreach ($cartItems as $item)
                    <div
                        style="position: relative; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); width: 300px; display: flex; flex-direction: column; justify-content: space-between;">
                        <!-- Delete Button (Cross Inside Circle) -->
                        <button
                            style="position: absolute; top: 5px; right: 5px; background: #E53E3E; border: none; color: #fff; font-size: 14px; cursor: pointer; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;"
                            wire:click="deleteCartItem({{ $item->id }})">
                            <i class="fas fa-times"></i>
                        </button>
                        <!-- Item Details -->
                        <div style="flex: 1; display: flex; align-items: center;">
                            <i class="fas fa-box" style="color: #6B46C1; font-size: 30px; margin-right: 15px;"></i>
                            <div>
                                <div style="font-size: 18px; font-weight: bold; color: #333;">
                                    {{ ucwords($item->product->name) }}
                                </div>
                                <div style="font-size: 14px; color: #666;">
                                    <i class="fas fa-store" style="margin-right: 5px;"></i>Supplier:
                                    {{ $item->shopstock->organization->name }}
                                </div>
                                <div style="font-size: 14px; color: #666;">
                                    <i class="fas fa-weight-hanging" style="margin-right: 5px;"></i>Quantity:
                                    {{ $item->quantity }} {{ $item->product->unit }}
                                </div>
                                <div style="font-size: 14px; color: #666;">
                                    <i class="fas fa-tag" style="margin-right: 5px;"></i>Price:
                                    ₹{{ number_format($item->shopstock->price, 2) }} per piece
                                </div>
                                <div style="font-size: 14px; font-weight: bold; color: #333; margin-top: 5px;">
                                    <i class="fas fa-calculator" style="margin-right: 5px;"></i>Total:
                                    ₹{{ number_format($item->quantity * $item->shopstock->price, 2) }}
                                </div>
                            </div>
                        </div>
                        <!-- Quantity Selection -->
                        <div style="display: flex; justify-content: center; align-items: center; margin-top: 10px;">
                            <button
                                style="background-color: {{ $item->quantity <= 1 ? '#CBD5E0' : '#7A31CE' }}; color: #fff; padding: 8px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center;"
                                wire:click="decreaseQuantity({{ $item->id }})"
                                @if ($item->quantity <= 1) disabled @endif>
                                <i class="fas fa-minus"></i>
                            </button>
                            <div
                                style="background-color: #319795; color: #fff; padding: 8px 16px; border-radius: 6px; margin: 0 10px; font-size: 16px; display: flex; align-items: center; justify-content: center;">
                                {{ $item->quantity }}
                            </div>
                            <button
                                style="background-color: {{ $item->quantity == $item->shopstock->quantity ? '#CBD5E0' : '#7A31CE' }}; color: #fff; padding: 8px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center;"
                                wire:click="increaseQuantity({{ $item->id }})"
                                @if ($item->quantity == $item->shopstock->quantity) disabled @endif>
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <!-- Place Order Button -->
                        <div style="margin-top: 20px;">
                            <button wire:click="placeOrder({{ $item->id }})"
                                style="background-color: #7A31CE; color: #fff; padding: 12px 18px; border-radius: 50px; border: none; cursor: pointer; font-weight: bold; font-size: 16px; width: 100%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-check-circle" style="margin-right: 8px;"></i>Place Order
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Grand Total Display -->
            <div
                style="margin-top: 30px; padding: 15px; border-radius: 8px; background-color: #f7fafc; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 80px;">
                <h3 style="font-size: 20px; font-weight: bold; color: #333; text-align: right;">
                    Grand Total:
                    ₹{{ number_format($cartItems->sum(fn($item) => $item->quantity * $item->shopstock->price), 2) }}
                </h3>
            </div>

            <!-- Sticky Place All Orders Button -->
            <div style="position: fixed; bottom: 20px; right: 20px;">
                <button wire:click="placeAllOrders"
                    style="background-color: #7A31CE; color: #fff; padding: 15px 30px; border-radius: 50px; border: none; cursor: pointer; font-weight: bold; font-size: 18px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                    <i class="fas fa-check-double" style="margin-right: 8px;"></i>Place All Orders
                </button>
            </div>
        @endif
    </div>

    <!-- Font Awesome Icon CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</x-filament-panels::page>
