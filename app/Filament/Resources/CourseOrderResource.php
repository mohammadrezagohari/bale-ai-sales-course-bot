<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseOrderResource\Pages;
use App\Models\CourseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseOrderResource extends Resource
{
    protected static ?string $model = CourseOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Course orders';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('course_id')->relationship('course', 'title')->required()->searchable(),
            Forms\Components\Select::make('lead_id')->relationship('lead', 'bale_user_id')->required()->searchable(),
            Forms\Components\TextInput::make('amount')->numeric()->required()->suffix('تومان'),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'cancelled' => 'Cancelled',
                    'refunded' => 'Refunded',
                ])
                ->required(),
            Forms\Components\TextInput::make('payment_reference'),
            Forms\Components\DateTimePicker::make('paid_at'),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('course.title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('lead.bale_user_id')->label('Bale user')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('amount')->numeric()->suffix(' تومان')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'cancelled', 'refunded' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('paid_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseOrders::route('/'),
            'create' => Pages\CreateCourseOrder::route('/create'),
            'edit' => Pages\EditCourseOrder::route('/{record}/edit'),
        ];
    }
}
