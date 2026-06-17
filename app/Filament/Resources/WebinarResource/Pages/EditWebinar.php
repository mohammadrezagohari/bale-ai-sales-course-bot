<?php

namespace App\Filament\Resources\WebinarResource\Pages;

use App\Filament\Resources\WebinarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebinar extends EditRecord
{
    protected static string $resource = WebinarResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
