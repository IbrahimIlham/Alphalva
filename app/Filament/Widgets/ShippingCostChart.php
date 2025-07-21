<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ShippingCostChart extends ChartWidget
{
    protected static ?string $heading = 'Shipping Cost Trends';

    protected function getData(): array
    {
        $days = collect();
        $shippingCosts = collect();

        // Get last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->format('M d'));
            
            $totalShippingCost = Order::whereDate('created_at', $date)
                ->whereNotNull('shipping_amount')
                ->sum('shipping_amount');
            
            $shippingCosts->push($totalShippingCost);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Daily Shipping Revenue (IDR)',
                    'data' => $shippingCosts->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
} 