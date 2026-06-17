<?php

namespace App\Filament\Resources\WebinarRegistrationResource\Pages;

use App\Filament\Resources\WebinarRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebinarRegistrations extends ListRecords
{
    protected static string $resource = WebinarRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
