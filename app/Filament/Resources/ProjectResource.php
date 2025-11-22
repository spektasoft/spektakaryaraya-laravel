<?php

namespace App\Filament\Resources;

use App\Enums\Project\Status;
use App\Filament\Actions\Tables\ReferenceAwareDeleteBulkAction;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\UserResource\Utils\Creator;
use App\Filament\Tables\Columns\TranslatableTextColumn;
use App\Forms\Components\LocalesAwareTranslate;
use App\Models\Project;
use App\Models\User;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class ProjectResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function canViewAll(): bool
    {
        return static::can('viewAll');
    }

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
                            ->suffixLocaleLabel()
                            ->schema(function (Get $get) {
                                /** @var array<string, string|null>|null $names */
                                $names = $get('name');
                                $required = collect($names ?? [])->every(fn ($item) => $item === null || trim((string) $item) === '');

                                return [
                                    Forms\Components\TextInput::make('name')
                                        ->label(__('project.resource.name'))
                                        ->lazy()
                                        ->required($required),
                                    TiptapEditor::make('description')
                                        ->label(__('project.resource.description')),
                                ];
                            }),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label(__('project.resource.start_date'))
                                    ->native(false)
                                    ->required(),
                                Forms\Components\TextInput::make('url')
                                    ->label(__('project.resource.url'))
                                    ->url()
                                    ->nullable(),
                                Forms\Components\Select::make('partners')
                                    ->label(__('project.resource.partners'))
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
                                    ->label(__('project.resource.status_label'))
                                    ->options(Status::class)
                                    ->required()
                                    ->default(Status::Draft->value),
                                App::runningUnitTests()
                                    ? Forms\Components\Hidden::make('logo_id')
                                    : CuratorPicker::make('logo_id')
                                        ->label(__('project.resource.logo'))
                                        ->relationship('logo', 'id'),
                            ]),
                        Creator::getComponent(static::canViewAll()),
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
                    ->label(__('project.resource.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('project.resource.start_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(ucfirst(__('validation.attributes.creator')))
                    ->searchable()
                    ->visible(static::canViewAll()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ReferenceAwareDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Administration');
    }

    public static function getModelLabel(): string
    {
        return trans_choice('project.resource.model_label', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('project.resource.model_label', 2);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<Project>
     */
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<Project> */
        $query = parent::getEloquentQuery();

        if (! static::canViewAll()) {
            $query->whereCreatorId(User::auth()?->id);
        }

        return $query;
    }

    /**
     * @return string[]
     */
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_all',
            'view_any',
            'create',
            'update',
            'delete',
        ];
    }
}
