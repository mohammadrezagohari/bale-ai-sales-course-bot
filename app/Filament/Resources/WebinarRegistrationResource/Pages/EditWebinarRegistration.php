<?php

namespace App\Filament\Resources\WebinarRegistrationResource\Pages;

use App\Filament\Resources\WebinarRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebinarRegistration extends EditRecord
{
    protected static string $resource = WebinarRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
