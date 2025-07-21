<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ShippingStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $ordersWithShipping = Order::whereNotNull('shipping_amount')->where('shipping_amount', '>', 0)->count();
        $totalShippingCost = Order::whereNotNull('shipping_amount')->sum('shipping_amount');
        $avgShippingCost = $ordersWithShipping > 0 ? $totalShippingCost / $ordersWithShipping : 0;

        // Shipping method breakdown
        $jneOrders = Order::where('shipping_method', 'jne')->count();
        $posOrders = Order::where('shipping_method', 'pos')->count();
        $tikiOrders = Order::where('shipping_method', 'tiki')->count();

        return [
            Stat::make('Total Orders', $totalOrders)
                ->description('All orders in system')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('Orders with Shipping', $ordersWithShipping)
                ->description('Orders with shipping cost')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),

            Stat::make('Total Shipping Revenue', Number::currency($totalShippingCost, 'IDR'))
                ->description('Total shipping costs collected')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make('Average Shipping Cost', Number::currency($avgShippingCost, 'IDR'))
                ->description('Average shipping cost per order')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make('JNE Orders', $jneOrders)
                ->description('Orders shipped via JNE')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),

            Stat::make('POS Orders', $posOrders)
                ->description('Orders shipped via POS Indonesia')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),

            Stat::make('TIKI Orders', $tikiOrders)
                ->description('Orders shipped via TIKI')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
        ];
    }
} 