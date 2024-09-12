<?php

namespace App\Filament\Owner\Widgets;

use App\Models\Order; // Assuming your orders are stored in a model named Order
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DishWidget extends ChartWidget
{
    protected static ?string $heading = 'Total Sales Orders This Year';

    //sort this widget in 2nd postion
    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'bar'; // You can change this to 'line', 'pie', etc. depending on your needs
    }

    protected function getData(): array
    {
        // Get the current year
        $currentYear = Carbon::now()->year;
        $organizationId = auth()->user()->organization->id;

        // Query to fetch the total orders grouped by month
        $orders = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('organization_id', $organizationId) // Filter by organization ID
            ->where('status', 'delivered')
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
                    'label' => 'Total Sales Orders',
                    'data' => $data,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
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
                    'color' => '#4BC0C0',
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
