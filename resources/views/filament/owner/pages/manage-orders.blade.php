<x-filament-panels::page>
    <style>
        /* Existing styles */
        .accordion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            background-color: #ffffff;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 5px;
            transition: background-color 0.3s ease;
            position: relative;
        }

        .accordion-header:hover {
            background-color: #f7fafc;
        }

        .accordion-details {
            max-height: 0;
            overflow: hidden;
            padding: 0 30px;
            border-radius: 8px;
            margin-left: 20px;
            background-color: #f7fafc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-left: 4px solid #7A31CE;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .accordion-details.active {
            max-height: 10000px;
            padding: 15px 30px;
        }

        .accordion-icon {
            transition: transform 0.3s ease;
        }

        .accordion-icon.rotate {
            transform: rotate(180deg);
        }

        .subtotal {
            font-size: 16px;
            font-weight: bold;
            color: #4A5568;
            text-align: right;
            margin-top: 15px;
        }

        /* Badge Styling Based on Status */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 12px;
            color: #fff;
        }

        .badge-pending {
            background-color: #ECC94B;
        }

        .badge-accepted {
            background-color: #48BB78;
        }


        .badge-shipped {
            background-color: #4299E1;
        }

        .badge-completed {
            background-color: #38A169;
        }

        .badge-rejected {
            background-color: #fd1302;
        }

        /* Cancel Button Styling */
        .cancel-button {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #F56565;
            color: white;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .cancel-button:hover {
            background-color: #E53E3E;
        }

        .custom-tooltip {
            visibility: hidden;
            width: 200px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            /* Position the tooltip above the button */
            left: 50%;
            margin-left: -100px;
            /* Center the tooltip */
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
        }

        .cancel-button:hover .custom-tooltip {
            visibility: visible;
            opacity: 1;
        }

        .order-review {
            font-size: 14px;
            color: #E53E3E;
            /* font-style: italic; */
            margin-top: 10px;
            padding: 10px;
            background-color: #fff5f5;
            border-left: 4px solid #F56565;
            border-radius: 4px;
        }

        /* Toast Notification Styles */
        #toast {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
        }

        #toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @keyframes fadein {
            from {
                bottom: 0;
                opacity: 0;
            }

            to {
                bottom: 30px;
                opacity: 1;
            }
        }

        @keyframes fadeout {
            from {
                bottom: 30px;
                opacity: 1;
            }

            to {
                bottom: 0;
                opacity: 0;
            }
        }

        /* Pagination Styles */
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

    <div style="max-width: 1000px; margin: 0 auto; padding: 20px;">
        <!-- Note about cancelled orders -->
        @php
            $hasRejectedOrders = false;
            foreach ($orders as $order) {
                if ($order->status == 'rejected') {
                    $hasRejectedOrders = true;
                    break;
                }
            }
        @endphp

        @if ($hasRejectedOrders)
            <!-- Note about rejected orders -->
            <div
                style="background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #ffeeba;">
                <strong>Note:</strong> Rejected orders will be removed within 24 hours.
            </div>
        @endif
        @if (empty($orders))
            <!-- No Orders Found Message -->
            <div style="text-align: center; color: #A0AEC0; margin-top: 50px;">
                <i class="fas fa-exclamation-circle" style="color: #CBD5E0; font-size: 60px; margin-bottom: 20px;"></i>
                <p>No orders found.</p>
            </div>
        @else
            <!-- Orders List -->
            <div style="margin-top: 30px;">
                @foreach ($orders as $order)
                    <div class="accordion-header" onclick="toggleAccordion({{ $order->id }})">
                        <div style="flex-grow: 1;">
                            <div style="font-size: 18px; font-weight: bold; color: #2D3748;">
                                <i class="fas fa-receipt" style="color: #7A31CE; margin-right: 8px;"></i>

                                <span onclick="copyToClipboard('{{ $order->supplier_order_id }}')"
                                    style="cursor: pointer;">
                                    {{ $order->supplier_order_id }}
                                </span>

                                &nbsp;&nbsp;
                                <span class="badge badge-{{ $order->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                            <div style="font-size: 14px; color: #718096; margin-top: 5px;">
                                <i class="fas fa-store" style="color: #7A31CE; margin-right: 5px;"></i>Supplier:
                                {{ $order->supplier->name }}
                            </div>
                            @if ($order->status == 'rejected')
                                <div class="order-review">
                                    {!! Str::markdown($order->review) !!}
                                </div>
                            @endif
                        </div>

                        @if ($order->status !== 'rejected')
                            <!-- Cancel Order Button -->
                            <form wire:submit.prevent="cancelOrder({{ $order->id }})" style="display:inline;">
                                <button type="submit" class="cancel-button"
                                    onclick="return confirm('Are you sure you want to cancel this order?')">
                                    &times;
                                    <span class="custom-tooltip">Cancel this Order</span>
                                </button>

                            </form>
                        @endif

                        <!-- Moving the icon inside the clickable area -->
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-chevron-down accordion-icon" id="accordion-icon-{{ $order->id }}"></i>
                        </div>
                    </div>
                    <div class="accordion-details" id="accordion-details-{{ $order->id }}">
                        <div>
                            @php
                                $subtotal = 0;
                            @endphp

                            @foreach ($order->details as $detail)
                                @php
                                    $subtotal += $detail->shopstock->price * $detail->quantity;
                                @endphp
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <div>
                                        <div style="font-size: 14px; font-weight: bold; color: #4A5568;">
                                            {{ ucwords($detail->product->name) }}
                                        </div>
                                        <div style="font-size: 12px; color: #718096;">
                                            <i class="fas fa-cubes"
                                                style="color: #7A31CE; margin-right: 5px;"></i>Quantity:
                                            {{ $detail->quantity }}
                                        </div>
                                    </div>
                                    <div style="font-size: 14px; font-weight: bold; color: #2D3748;">
                                        ₹.{{ number_format($detail->shopstock->price * $detail->quantity, 2) }}
                                    </div>
                                </div>
                                <hr>
                            @endforeach

                            @if (count($order->details) > 1)
                                <!-- Subtotal Display -->
                                <div class="subtotal">
                                    Subtotal: ₹.{{ number_format($subtotal, 2) }}
                                </div>
                            @endif


                        </div>
                    </div>
                @endforeach
            </div>
        @endif

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
    </div>

    <script>
        function toggleAccordion(id) {
            var details = document.getElementById('accordion-details-' + id);
            var icon = document.getElementById('accordion-icon-' + id);

            if (details.classList.contains('active')) {
                details.style.maxHeight = details.scrollHeight + 20 + 'px';
                setTimeout(function() {
                    details.style.maxHeight = '0';
                }, 10); // Delay to allow height to adjust
                details.classList.remove('active');
                icon.classList.remove('rotate');
            } else {
                details.style.maxHeight = details.scrollHeight + 20 + 'px';
                details.classList.add('active');
                icon.classList.add('rotate');
            }
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Order ID copied to clipboard: ' + text,
                    timer: 1500,
                    showConfirmButton: false
                });
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>

    <!-- Font Awesome Icon CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</x-filament-panels::page>
