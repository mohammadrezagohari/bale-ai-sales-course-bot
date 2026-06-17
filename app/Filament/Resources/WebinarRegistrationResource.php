<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebinarRegistrationResource\Pages;
use App\Models\WebinarRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebinarRegistrationResource extends Resource
{
    protected static ?string $model = WebinarRegistration::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Webinar registrations';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('webinar_id')->relationship('webinar', 'title')->required()->searchable(),
            Forms\Components\Select::make('lead_id')->relationship('lead', 'bale_user_id')->required()->searchable(),
            Forms\Components\Select::make('status')
                ->options([
                    'registered' => 'Registered',
                    'attended' => 'Attended',
                    'cancelled' => 'Cancelled',
                ])
                ->required(),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('webinar.title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('lead.bale_user_id')->label('Bale user')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebinarRegistrations::route('/'),
            'create' => Pages\CreateWebinarRegistration::route('/create'),
            'edit' => Pages\EditWebinarRegistration::route('/{record}/edit'),
        ];
    }
}
