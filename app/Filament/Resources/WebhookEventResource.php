<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebhookEventResource\Pages;
use App\Models\WebhookEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebhookEventResource extends Resource
{
    protected static ?string $model = WebhookEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Webhook events';

    protected static ?string $navigationGroup = 'Bot';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('event_id')
                ->disabled(),
            Forms\Components\Textarea::make('payload')
                ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                ->rows(16)
                ->disabled()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event_id')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payload.message.text')
                    ->label('Message')
                    ->limit(55)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebhookEvents::route('/'),
            'view' => Pages\ViewWebhookEvent::route('/{record}'),
        ];
    }
}
