<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\LineChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OrdersChart extends LineChartWidget
{
    protected static ?int $sort = 1;
    protected static ?string $heading = "Orders per month";

    protected function getData(): array
    {
        $data = Trend::model(Order::class)
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->count();

        return [
            "datasets" => [
                [
                    "label" => "Orders",
                    "data" => $data->map(
                        fn(TrendValue $value) => $value->aggregate
                    ),
                ],
            ],
            "labels" => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }
}