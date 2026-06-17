<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\CourseOrder;
use App\Models\WebhookEvent;
use App\Models\WebinarRegistration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BotStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total leads', Lead::count())
                ->description('All captured Bale users')
                ->color('success'),
            Stat::make('Course leads', Lead::whereIn('intent', ['course_list', 'course_purchase'])->count())
                ->description('Users interested in courses')
                ->color('primary'),
            Stat::make('Paid sales', CourseOrder::where('status', 'paid')->count())
                ->description(number_format((int) CourseOrder::where('status', 'paid')->sum('amount')).' تومان')
                ->color('success'),
            Stat::make('Pending orders', CourseOrder::where('status', 'pending')->count())
                ->description('Orders created from Bale bot')
                ->color('warning'),
            Stat::make('Webinar signups', Lead::where('intent', 'webinar')->count())
                ->description(WebinarRegistration::count().' registered users')
                ->color('warning'),
            Stat::make('Webhook events', WebhookEvent::count())
                ->description('Received Bale updates')
                ->color('gray'),
        ];
    }
}
