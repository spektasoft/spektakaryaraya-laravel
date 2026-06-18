<?php

namespace App\Filament\Resources\Projects;

use App\Enums\Project\Status;
use App\Filament\Actions\Tables\ReferenceAwareDeleteBulkAction;
use App\Filament\Forms\Components\CuratorEnabledRichEditor;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Users\Utils\Creator;
use App\Filament\Tables\Columns\TranslatableTextColumn;
use App\Forms\Components\LocalesAwareTranslate;
use App\Models\Project;
use App\Models\User;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    public static function canViewAll(): bool
    {
        return static::can('viewAll');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'sm' => 6,
                ])->schema([
                    Group::make([
                        LocalesAwareTranslate::make()
                            ->suffixLocaleLabel()
                            ->schema(function (Get $get) {
                                /** @var array<string, string|null>|null $names */
                                $names = $get('name');
                                $required = collect($names ?? [])->every(fn ($item) => $item === null || trim((string) $item) === '');

                                return [
                                    TextInput::make('name')
                                        ->label(__('project.resource.name'))
                                        ->lazy()
                                        ->required($required),
                                    CuratorEnabledRichEditor::make('description')
                                        ->label(__('project.resource.description')),
                                ];
                            }),
                        Section::make()
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label(__('project.resource.start_date'))
                                    ->native(false)
                                    ->required(),
                                TextInput::make('url')
                                    ->label(__('project.resource.url'))
                                    ->url()
                                    ->nullable(),
                                Select::make('partners')
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
                    Group::make([
                        Section::make()
                            ->schema([
                                Radio::make('status')
                                    ->label(__('project.resource.status_label'))
                                    ->options(Status::class)
                                    ->required()
                                    ->default(Status::Draft->value),
                                App::runningUnitTests()
                                    ? Hidden::make('logo_id')
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
                TextColumn::make('start_date')
                    ->label(__('project.resource.start_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(ucfirst(__('validation.attributes.creator')))
                    ->searchable()
                    ->visible(static::canViewAll()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
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
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
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

        return $query->with(['creator']);
    }
}
