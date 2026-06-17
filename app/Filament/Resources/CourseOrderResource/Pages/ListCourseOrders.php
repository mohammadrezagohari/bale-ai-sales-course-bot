<?php

namespace App\Filament\Resources\CourseOrderResource\Pages;

use App\Filament\Resources\CourseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseOrders extends ListRecords
{
    protected static string $resource = CourseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
