<x-filament-panels::page>
    <div style="margin: 20px auto; max-width: 800px;">
        <!-- Page Title and Search Form -->
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="font-size: 24px; font-weight: bold; color: #2D3748;">Order Tracking History</h2>
            <p style="color: #718096;">Search for an order by Order ID.</p>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <p style="color: #2D3748;">
                <strong>Note:</strong> Click on the Order ID to copy it. Example: <span
                    style="background-color: #EDF2F7; padding: 2px 5px; border-radius: 4px; font-family: monospace;">ODR-XXX-XXXXXXXX-XXXXXX-XXX</span>
            </p>
        </div>

        <!-- Search Form -->
        <div style="text-align: center; margin-bottom: 20px;">
            <input type="text" wire:model.live="search" placeholder="Enter Order ID "
                style="border: 2px solid #CBD5E0; border-radius: 8px; padding: 10px 15px; width: 80%; max-width: 400px; font-size: 16px; outline: none; transition: border-color 0.3s ease;">
        </div>

        <!-- Display Tracking History -->
        @if ($order)
            <div style="background-color: #ffffff; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px;">
                <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #2D3748;">
                    Tracking History for Order #{{ $order->supplier_order_id }}
                </h3>

                <div style="position: relative; padding-left: 40px;">
                    <!-- Timeline Vertical Line -->
                    <div
                        style="position: absolute; top: 0; bottom: 0; left: 24px; width: 2px; background-color: #CBD5E0;">
                    </div>

                    @foreach ($trackings as $tracking)
                        @php
                            $color = match ($tracking->status) {
                                'pending' => '#4299E1',
                                'accepted' => '#84CC16',
                                'shipped' => '#ED8936',
                                'completed' => '#38A169',
                                'rejected', 'cancelled' => '#E53E3E',
                                default => '#718096',
                            };
                            $icon = match ($tracking->status) {
                                'pending' => 'fas fa-hourglass-start',
                                'accepted' => 'fas fa-check-circle',
                                'shipped' => 'fas fa-shipping-fast',
                                'completed' => 'fas fa-box-open',
                                'rejected' => 'fas fa-times-circle',
                                'cancelled' => 'fas fa-ban',
                                default => 'fas fa-info-circle',
                            };
                        @endphp

                        <div style="display: flex; align-items: center; margin-bottom: 20px;">
                            <!-- Timeline Dot with Icon -->
                            <div
                                style="width: 24px; height: 24px; border-radius: 50%; background-color: {{ $color }}; margin-right: 25px; display: flex; align-items: center; justify-content: center; position: relative;">
                                <i class="{{ $icon }}" style="color: white;"></i>
                            </div>

                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 16px; font-weight: bold; color: {{ $color }};">
                                        @if ($tracking->status == 'completed')
                                            {{ ucwords('Delivered') }}
                                        @else
                                            {{ ucwords(str_replace('_', ' ', $tracking->status)) }}
                                        @endif

                                    </span>
                                    <span style="font-size: 14px; color: #718096;">
                                        {{ $tracking->created_at->format('d M, Y h:i:s A') }}
                                    </span>
                                </div>
                                <p style="font-size: 14px; color: #4A5568; margin-top: 5px;">
                                    {{ $tracking->description }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($search)
            <div
                style="background-color: #ffffff; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px; margin-top: 20px;">
                <p style="color: #e53e3e; text-align: center;">No order found with the given Supplier Order ID.</p>
            </div>
        @endif
    </div>

    <!-- Font Awesome Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</x-filament-panels::page>
