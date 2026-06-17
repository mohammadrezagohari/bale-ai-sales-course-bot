<?php

namespace App\Filament\Resources\CourseOrderResource\Pages;

use App\Filament\Resources\CourseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourseOrder extends EditRecord
{
    protected static string $resource = CourseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
