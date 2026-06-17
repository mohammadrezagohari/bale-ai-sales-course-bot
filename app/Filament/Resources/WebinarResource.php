<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebinarResource\Pages;
use App\Models\Webinar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebinarResource extends Resource
{
    protected static ?string $model = Webinar::class;
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationLabel = 'Webinars';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
            Forms\Components\DateTimePicker::make('starts_at'),
            Forms\Components\TextInput::make('registration_url')->url()->columnSpanFull(),
            Forms\Components\RichEditor::make('description')->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('registrations_count')->counts('registrations')->label('Registrations'),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('starts_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebinars::route('/'),
            'create' => Pages\CreateWebinar::route('/create'),
            'edit' => Pages\EditWebinar::route('/{record}/edit'),
        ];
    }
}
