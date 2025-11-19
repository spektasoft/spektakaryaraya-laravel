<?php

namespace App\Filament\Resources;

use App\Enums\Project\Status;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Tables\Columns\TranslatableTextColumn;
use App\Forms\Components\LocalesAwareTranslate;
use App\Models\Project;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'sm' => 6,
                ])->schema([
                    Forms\Components\Group::make([
                        LocalesAwareTranslate::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                TiptapEditor::make('description'),
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->native(false)
                                    ->required(),
                                Forms\Components\TextInput::make('url')
                                    ->url()
                                    ->nullable(),
                                Forms\Components\Select::make('partners')
                                    ->relationship('partners', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ]),
                    ])->columnSpan([
                        'default' => 1,
                        'sm' => 4,
                    ]),
                    Forms\Components\Group::make([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Radio::make('status')
                                    ->options(Status::class)
                                    ->required()
                                    ->default(Status::Draft->value),
                                CuratorPicker::make('logo_id')
                                    ->relationship('logo', 'id'),
                            ]),
                    ])->columnSpan([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('logo')
                    ->size(40),
                TranslatableTextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
