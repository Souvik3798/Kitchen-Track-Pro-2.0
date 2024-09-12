<?php

namespace App\Filament\Owner\Widgets;

use App\Models\ShopOrder; // Assuming your orders are stored in a model named ShopOrder
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseOrders extends ChartWidget
{
    protected static ?string $heading = 'Total Purchase Orders This Year';

    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'line'; // You can change this to 'line', 'pie', etc. depending on your needs
    }

    protected function getData(): array
    {
        // Get the current year
        $currentYear = Carbon::now()->year;

        // Get the organization ID of the current user
        $organizationId = auth()->user()->organization->id;

        // Query to fetch the total orders grouped by month for the current organization
        $orders = ShopOrder::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('organization_id', $organizationId) // Filter by organization ID
            ->where('status', 'completed')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Prepare the data for the chart
        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('F'); // Get the month name
            $data[] = $orders->firstWhere('month', $i)->total ?? 0; // Get the total for each month, default to 0
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Orders',
                    'data' => $data,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)', // Change background color to a red shade
                    'borderColor' => 'rgba(255, 99, 132, 1)',        // Change border color to a red shade
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'datalabels' => [
                    'display' => true,
                    'anchor' => 'end',
                    'align' => 'top',
                    'color' => 'red', // Change the label color to red
                    'font' => [
                        'weight' => 'bold',
                    ],
                    'formatter' => function ($value, $context) {
                        return $value > 0 ? $value : ''; // Show value only if greater than 0
                    },
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
