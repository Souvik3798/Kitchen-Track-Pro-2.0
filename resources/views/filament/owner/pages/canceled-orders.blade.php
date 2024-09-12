<x-filament-panels::page>
    <style>
        .accordion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            padding: 15px 20px;
            border-radius: 8px;
            cursor: pointer;
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
            border-left: 4px solid #F56565;
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

        /* Badge Styling for Canceled Status */
        .badge-cancelled {
            background-color: #F56565;
            color: white;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif
        }

        /* Delete Button Styling */
        .delete-button {
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

        .delete-button:hover {
            background-color: #E53E3E;
        }


        /* Reverse Button Styling */
        .reverse-button {
            position: absolute;
            top: -10px;
            left: -10px;
            background-color: #485dbb;
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

        .reverse-button:hover {
            background-color: #38A169;
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

        .reverse-button:hover .custom-tooltip {
            visibility: visible;
            opacity: 1;
        }

        .delete-button:hover .custom-tooltip {
            visibility: visible;
            opacity: 1;
        }
    </style>

    <!-- Container for the entire canceled orders page -->
    <div style="max-width: 1000px; margin: 0 auto; padding: 20px;">
        @if ($orders->isEmpty())
            <!-- No Orders Found Message -->
            <div style="text-align: center; color: #A0AEC0; margin-top: 50px;">
                <i class="fas fa-exclamation-circle" style="color: #CBD5E0; font-size: 60px; margin-bottom: 20px;"></i>
                <p>No canceled orders found.</p>
            </div>
        @else
            <!-- Orders List -->
            <div style="margin-top: 30px;">
                @foreach ($orders as $order)
                    <!-- Accordion Header -->
                    <div class="accordion-header">
                        <div style="flex-grow: 1;" onclick="toggleAccordion({{ $order->id }})">
                            <div style="font-size: 18px; font-weight: bold; color: #2D3748;">
                                <i class="fas fa-receipt" style="color: #F56565; margin-right: 8px;"></i>
                                <span onclick="copyToClipboard('{{ $order->supplier_order_id }}')"
                                    style="cursor: pointer;">
                                    {{ $order->supplier_order_id }}
                                </span>

                                &nbsp;&nbsp;&nbsp;
                                <span class="badge badge-cancelled">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div style="font-size: 14px; color: #718096; margin-top: 5px;">
                                <i class="fas fa-store" style="color: #F56565; margin-right: 5px;"></i>Supplier:
                                {{ $order->supplier->name }}
                            </div>
                        </div>

                        <!-- Delete Order Button -->
                        <form wire:submit.prevent="deleteOrder({{ $order->id }})" style="display:inline;">
                            <button type="submit" class="delete-button"
                                onclick="return confirm('Are you sure you want to delete this order? This action cannot be undone.')">
                                <i class="fas fa-trash"></i>
                                <span class="custom-tooltip">Permanently Delete this Order</span>
                            </button>
                        </form>

                        <!-- Reverse Order Button -->
                        <form wire:submit.prevent="reverseOrder({{ $order->id }})" style="display:inline;">
                            <button type="submit" class="reverse-button">
                                <i class="fas fa-repeat"></i>
                                <span class="custom-tooltip">Reorder It</span>
                            </button>
                        </form>
                        <div>
                            <i class="fas fa-chevron-down accordion-icon" id="accordion-icon-{{ $order->id }}"
                                onclick="toggleAccordion({{ $order->id }})"></i>
                        </div>
                    </div>

                    <!-- Accordion Details -->
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
                                                style="color: #F56565; margin-right: 5px;"></i>Quantity:
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
    </div>

    <!-- JavaScript for Accordion with Slide Animation -->
    <script>
        function toggleAccordion(id) {
            var details = document.getElementById('accordion-details-' + id);
            var icon = document.getElementById('accordion-icon-' + id);

            if (details.classList.contains('active')) {
                details.style.maxHeight = details.scrollHeight + 20 + 'px';
                setTimeout(function() {
                    details.style.maxHeight = '0';
                }, 10);
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
