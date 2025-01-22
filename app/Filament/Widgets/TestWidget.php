<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TestWidget extends BaseWidget
{

    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        // Fetch user data grouped by creation date
        $usersData = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
        // Convert the data into a format suitable for the chart
        $chartData = $usersData->values()->toArray(); // Counts
        //$chartLabels = $usersData->keys()->toArray(); // Dates

        // Determine the chart color based on the latest data
        $latestCount = end($chartData); // Get the latest user count
        $chartColor = $this->getChartColor($latestCount);

        // For Posts
        $postsData = Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
        $chartPostData = $postsData->values()->toArray(); // Counts

        $latestPostCount = end($chartPostData); // Get the latest post count
        $chartPostColor = $this->getChartColor($latestPostCount);

        $stDate = $this->filters['startDate'];
        $ndDate =  $this->filters['endDate'];
        return [
            Stat::make(
                'New Users',
                User::when(
                    $stDate,
                    fn($query) =>
                    $query->whereDate(
                        'created_at',
                        '>',
                        $stDate
                    )
                )
                    ->when(
                        $ndDate,
                        fn($query) =>
                        $query->whereDate(
                            'created_at',
                            '<',
                            $ndDate
                        )
                    )->count()
            )
                ->description('Newly joined users')
                ->descriptionIcon('heroicon-o-user-group', position: IconPosition::After)
                ->chart($chartData) // Pass the chart data
                ->color($chartColor), // Set the chart color dynamically

            Stat::make('Posts', Post::count())
                ->description('Newly added posts')
                ->descriptionIcon('heroicon-o-tag', position: IconPosition::After)
                ->chart($chartPostData) // Pass the chart data
                ->color($chartPostColor) // Set the chart color dynamically
        ];
    }
    /**
     * Determine the chart color based on the latest user count.
     */
    protected function getChartColor(int $latestCount): string
    {
        if ($latestCount > 10) {
            return 'success'; // Green for high growth
        } elseif ($latestCount > 5) {
            return 'warning'; // Yellow for moderate growth
        } else {
            return 'danger'; // Red for low growth
        }
    }
}
