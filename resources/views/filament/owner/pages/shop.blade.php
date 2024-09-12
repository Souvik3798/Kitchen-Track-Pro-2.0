<x-filament-panels::page>
    <style>
        /* Existing styles */
        .card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 16px;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card-content {
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .org-name {
            background-color: #f7fafc;
            color: #7a31ce;
            padding: 8px;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        h3 {
            font-size: 20px;
            font-weight: 700;
            margin: 8px 0;
            color: #2d3748;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        p {
            font-size: 14px;
            color: #4a5568;
            margin: 4px 0;
        }

        .btn {
            margin-top: 16px;
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            background-color: #7a31ce;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn:hover {
            background-color: #51119b;
        }

        .btn:disabled {
            background-color: #e2e8f0;
            color: #a0aec0;
            cursor: not-allowed;
        }

        /* General Dropdown Styles */
        .quantity-wrapper {
            display: flex;
            align-items: center;
            margin-top: 16px;
        }

        .quantity-btn {
            background-color: #5a21b6;
            border: 1px solid #cbd5e0;
            padding: 10px 16px;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .quantity-btn:hover {
            background-color: #3b1080;
        }

        .quantity-input {
            text-align: center;
            width: 60px;
            padding: 10px;
            border: 1px solid #cbd5e0;
            border-radius: 5px;
            margin: 0 8px;
            font-weight: bold;
            background-color: #319795;
            color: #ffffff;
            pointer-events: none;
        }

        .quantity-btn:disabled {
            background-color: #e2e8f0;
            color: #a0aec0;
            cursor: not-allowed;
        }

        .outofstock {
            background-color: #fff5f5;
            color: #e53e3e;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            border: 1px solid #e53e3e;
            margin-top: 10px;
        }

        .search-input {
            margin-bottom: 20px;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #cbd5e0;
            font-size: 16px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .icon {
            margin-right: 8px;
        }

        .sticky-cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #7a31ce;
            color: #fff;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            cursor: pointer;
            z-index: 1000;
            transition: background-color 0.3s;
        }

        .sticky-cart:hover {
            background-color: #6b31c1;
        }

        .sticky-cart i {
            margin-right: 10px;
        }

        /* pagination */

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination {
            list-style-type: none;
            display: flex;
            padding: 0;
            margin: 0;
            align-items: center;
        }

        .pagination li {
            margin: 0 3px;
        }

        .pagination a {
            text-decoration: none;
            color: #333;
            background-color: #f0f0f0;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination .active a {
            background-color: #007bff;
            color: white;
        }

        .pagination .disabled a {
            color: #999;
            pointer-events: none;
        }

        .pagination .ellipsis {
            padding: 0 5px;
        }
    </style>

    <!-- Font Awesome Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <div>
        <!-- Live Search Input -->
        <input type="text" wire:model.live="search" class="search-input" placeholder="Search items...">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($shopstocks as $shopstock)
            <div class="card">
                <div class="card-content">
                    <div class="org-name">
                        <i class="fas fa-store icon"></i>{{ $shopstock->organization->name }}
                    </div>
                    <h3><i class="fas fa-box-open icon"></i>{{ ucwords($shopstock->inventory->name) }}</h3>
                    <p><i class="fas fa-cubes icon"></i>Available Quantity: <span class="font-bold">
                            @if ($shopstock->quantity >= 1)
                                {{ $shopstock->quantity }} {{ $shopstock->inventory->unit }}
                            @endif
                        </span>
                    </p>
                    <p class="text-lg font-bold mt-4"><i class="fas fa-rupee-sign icon"></i>Price:
                        ₹{{ $shopstock->price }}/- <span class="text-sm"><i>Per Unit</i></span></p>

                    <!-- New Address Section -->
                    <p><i class="fas fa-map-marker-alt icon"></i>Address:
                        {{ $shopstock->organization->address ?? 'Not Available' }}
                    </p>

                    <!-- New Contact Section with WhatsApp Link -->
                    <p><i class="fas fa-phone-alt icon"></i>Contact:
                        <a href="https://wa.me/{{ $shopstock->organization->contact_number }}" target="_blank">
                            {{ $shopstock->organization->contact_number ?? 'Not Available' }}
                        </a>
                    </p>

                    @if ($shopstock->quantity >= 1)
                        <div class="quantity-wrapper">
                            <button class="quantity-btn" wire:click="decrementQuantity({{ $shopstock->id }})"
                                @if ($shopstock->quantity <= 1) disabled @endif><i class="fas fa-minus"></i></button>
                            <input type="text" class="quantity-input"
                                value="{{ $selectedQuantities[$shopstock->id] ?? 1 }}" readonly>
                            <button class="quantity-btn" wire:click="incrementQuantity({{ $shopstock->id }})"
                                @if ($shopstock->quantity <= ($selectedQuantities[$shopstock->id] ?? 1)) disabled @endif><i class="fas fa-plus"></i></button>
                        </div>
                    @else
                        <div class="outofstock">
                            <i class="fas fa-exclamation-circle icon"></i>Out of Stock
                        </div>
                    @endif

                    <button class="btn" @if ($shopstock->quantity < 1) disabled @endif
                        wire:click="addToCart({{ $shopstock->id }}, {{ $shopstock->inventory->id }})">
                        <i class="fas fa-cart-plus icon"></i>Add to Cart
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination Links -->
    <div class="pagination-container">
        @if ($paginationData['last_page'] > 1)
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <!-- First Page Link -->
                    <li class="{{ $paginationData['current_page'] <= 1 ? 'disabled' : '' }}">
                        <a href="#" wire:click.prevent="goToPage(1)" aria-label="First">
                            <span aria-hidden="true">First Page</span>
                        </a>
                    </li>

                    <!-- Previous Page Link -->
                    <li class="{{ $paginationData['current_page'] <= 1 ? 'disabled' : '' }}">
                        <a href="#" wire:click.prevent="goToPage({{ $paginationData['current_page'] - 1 }})"
                            aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    @php
                        $start = max($paginationData['current_page'] - 2, 1);
                        $end = min($start + 4, $paginationData['last_page']);

                        if ($end - $start < 4) {
                            $start = max($end - 4, 1);
                        }
                    @endphp

                    @if ($start > 1)
                        <li class="ellipsis">...</li>
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        <li class="{{ $paginationData['current_page'] == $page ? 'active' : '' }}">
                            <a href="#"
                                wire:click.prevent="goToPage({{ $page }})">{{ $page }}</a>
                        </li>
                    @endfor

                    @if ($end < $paginationData['last_page'])
                        <li class="ellipsis">...</li>
                    @endif

                    <!-- Next Page Link -->
                    <li
                        class="{{ $paginationData['current_page'] >= $paginationData['last_page'] ? 'disabled' : '' }}">
                        <a href="#" wire:click.prevent="goToPage({{ $paginationData['current_page'] + 1 }})"
                            aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>

                    <!-- Last Page Link -->
                    <li
                        class="{{ $paginationData['current_page'] >= $paginationData['last_page'] ? 'disabled' : '' }}">
                        <a href="#" wire:click.prevent="goToPage({{ $paginationData['last_page'] }})"
                            aria-label="Last">
                            <span aria-hidden="true">Last Page</span>
                        </a>
                    </li>
                </ul>
            </nav>
        @endif
    </div>

    <!-- Sticky Cart Icon -->
    <div class="sticky-cart" wire:click="goToCart">
        <i class="fas fa-shopping-cart"></i>
        Cart ({{ $cartCount }})
    </div>

</x-filament-panels::page>
