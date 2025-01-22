<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TestChartWidget extends ChartWidget
{

    use InteractsWithPageFilters;

    protected static ?string $heading = 'Chart';

    protected function getType(): string
    {
        return 'line';
        //return 'doughnut';
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'];
        $endDate = $this->filters['endDate'];

        //? using manual for chart
        // $postsData = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
        //     ->groupBy('date')
        //     ->orderBy('date')
        //     ->pluck('count', 'date');
        // $chartPostData = $postsData->values()->toArray();
        // return [
        //     'datasets' => [
        //         [
        //             'label' => 'Blog posts created',
        //             'data' => $chartPostData,
        //             //  'tension' => 0.1,
        //             // 'fill' => true,
        //             // 'borderColor'=> ''
        //         ],
        //     ],
        //     'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        // ];

        //? Fetch user counts by role //for dount
        // $adminCount = User::where('role', 'ADMIN')->count();
        // $editorCount = User::where('role', 'EDITOR')->count();
        // $normalCount = User::where('role', 'USER')->count();

        // return [
        //     'labels' => [
        //         'Admin',
        //         'Editor',
        //         'User',
        //     ],
        //     'datasets' => [
        //         [
        //             'label' => 'User Roles',
        //             'data' => [$adminCount, $editorCount, $normalCount], // Pass the counts
        //             'backgroundColor' => [
        //                 'rgb(255, 99, 132)', // Red for admin
        //                 'rgb(54, 162, 235)', // Blue for editor
        //                 'rgb(255, 205, 86)', // Yellow for normal
        //             ],
        //             'hoverOffset' => 4,
        //         ],
        //     ],
        // ];
        $labelStartDate =  $startDate ? Carbon::parse($startDate) : now()->subMonths(6);
        $labelEndDate = $endDate ? Carbon::parse($endDate) : now();

        //?using external library for chart 
        $data = Trend::model(User::class)
            ->between(
                start: $labelStartDate,
                end: $labelEndDate,
            )
            ->perMonth()
            ->count();
        //to see how data looks
        //dd($data);


        return [
            'datasets' => [
                [
                    'label' => "Users Year " . $labelStartDate->format('Y') . "/" . $labelEndDate->format('Y'),
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->format('M j')),
        ];
    }

    // //for dounght
    // protected function getOptions(): array
    // {
    //     return [
    //         'responsive' => true,
    //         'plugins' => [
    //             'legend' => [
    //                 'position' => 'top',
    //             ],
    //             'title' => [
    //                 'display' => true,
    //                 'text' => 'User Roles Distribution',
    //             ],
    //         ],
    //     ];
    // }
}
