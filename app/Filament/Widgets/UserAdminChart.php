<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UserAdminChart extends ChartWidget
{
    protected static ?string $heading = 'Users Chart';

    protected static ?int $sort = 2;

    // protected static ?string $maxHeight = '300px';
    protected static bool $isLazy = true;
    protected static string $color = 'success';
    //  protected static ?string $description = 'Users chart';

    //position of the writing with the check box
    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'position' => 'bottom',
            ],
        ],
    ];
    public ?string $filter = 'today';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $startDate = now()->subDays(4);
        $endDate = now();

        if ($activeFilter === 'week') {
            $startDate = now()->subWeek();
            $endDate = now();
        } elseif ($activeFilter === 'month') {
            $startDate = now()->subMonth();
            $endDate = now();
        } elseif ($activeFilter === 'year') {
            $startDate = now()->subYear();
            $endDate = now();
        }

        $data = Trend::model(User::class)
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->format('M j')),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
