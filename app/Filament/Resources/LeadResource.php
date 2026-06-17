<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Leads';

    protected static ?string $navigationGroup = 'Bot';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Bale user')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('first_name')->disabled(),
                    Forms\Components\TextInput::make('last_name')->disabled(),
                    Forms\Components\TextInput::make('username')->disabled(),
                    Forms\Components\TextInput::make('bale_user_id')->disabled(),
                    Forms\Components\TextInput::make('chat_id')->disabled(),
                    Forms\Components\TextInput::make('intent')->disabled(),
                ]),
            Forms\Components\Section::make('Message')
                ->schema([
                    Forms\Components\Textarea::make('last_message')
                        ->rows(4)
                        ->disabled(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Name')
                    ->formatStateUsing(fn ($record) => trim($record->first_name.' '.$record->last_name) ?: '-')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('bale_user_id')
                    ->label('Bale ID')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('intent')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'course' => 'success',
                        'webinar' => 'warning',
                        'consultant' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_message')
                    ->limit(45)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('intent')
                    ->options([
                        'course' => 'Course',
                        'webinar' => 'Webinar',
                        'technical_support' => 'Technical support',
                        'consultant' => 'Consultant',
                    ]),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
        ];
    }
}
