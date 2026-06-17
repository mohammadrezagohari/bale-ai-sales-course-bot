<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Courses';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Course')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('price')->numeric()->default(0)->suffix('تومان'),
                    Forms\Components\TextInput::make('duration'),
                    Forms\Components\TextInput::make('level'),
                    Forms\Components\DateTimePicker::make('starts_at'),
                    Forms\Components\TextInput::make('checkout_url')->url()->columnSpanFull(),
                    Forms\Components\Textarea::make('summary')->rows(3)->columnSpanFull(),
                    Forms\Components\RichEditor::make('description')->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('price')->numeric()->suffix(' تومان')->sortable(),
                Tables\Columns\TextColumn::make('orders_count')->counts('orders')->label('Orders'),
                Tables\Columns\TextColumn::make('paid_orders_count')
                    ->counts(['orders as paid_orders_count' => fn ($query) => $query->where('status', 'paid')])
                    ->label('Paid'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
