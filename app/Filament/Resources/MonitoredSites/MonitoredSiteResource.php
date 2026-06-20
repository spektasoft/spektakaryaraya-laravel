<?php

namespace App\Filament\Resources\MonitoredSites;

use App\Enums\MonitoredSite\Status;
use App\Filament\Resources\Users\Utils\Creator;
use App\Filament\Tables\Columns\TranslatableTextColumn;
use App\Forms\Components\LocalesAwareTranslate;
use App\Jobs\CheckSiteIntegrityJob;
use App\Jobs\CheckSiteUptimeJob;
use App\Models\MonitoredSite;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MonitoredSiteResource extends Resource
{
    protected static ?string $model = MonitoredSite::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    public static function canViewAll(): bool
    {
        return static::can('viewAll');
    }

    /**
     * @return Builder<MonitoredSite>
     */
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<MonitoredSite> */
        $query = parent::getEloquentQuery();

        if (! static::canViewAll()) {
            // Use a closure to group the WHERE conditions correctly
            $query->where(function (Builder $query) {
                $query->where('user_id', Auth::id())
                    ->orWhere('creator_id', Auth::id());
            });
        }

        return $query;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('monitoring.resource.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('monitoring.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('monitoring.resource.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('monitoring.resource.sections.general'))
                    ->schema([
                        LocalesAwareTranslate::make()
                            ->schema(function (Get $get) {
                                /** @var array<?string> */
                                $names = $get('name');
                                $required = collect($names)->every(fn ($item) => $item === null || trim($item) === '');

                                return [
                                    TextInput::make('name')
                                        ->label(__('monitoring.resource.fields.name'))
                                        ->lazy()
                                        ->required($required),
                                ];
                            })
                            ->columnSpanFull()
                            ->suffixLocaleLabel(),
                        TextInput::make('url')
                            ->label(__('monitoring.resource.fields.url'))
                            ->url()
                            ->required()
                            ->maxLength(255),
                        Select::make('project_id')
                            ->label(__('monitoring.resource.fields.project'))
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload(),
                        Section::make([
                            Radio::make('status')
                                ->default(Status::Active)
                                ->options(Status::class)
                                ->required(),
                        ])->label(__('monitoring.resource.fields.is_active')),
                        Creator::getComponent(static::canViewAll()),
                    ])->columns(2),

                Section::make(__('monitoring.resource.sections.baselines'))
                    ->schema([
                        TextInput::make('expected_md5_hash')
                            ->label(__('monitoring.resource.fields.md5_hash'))
                            ->disabled()
                            ->placeholder(__('monitoring.resource.fields.md5_hash_placeholder')),
                        TextInput::make('expected_links_count')
                            ->label(__('monitoring.resource.fields.links_count'))
                            ->numeric()
                            ->disabled(),
                        TextInput::make('expected_scripts_count')
                            ->label(__('monitoring.resource.fields.scripts_count'))
                            ->numeric()
                            ->disabled(),
                    ])->columns(3),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TranslatableTextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label(__('monitoring.resource.fields.url'))
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('uptime_status')
                    ->label(__('monitoring.resource.columns.uptime'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'up' => 'success',
                        'down' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('integrity_status')
                    ->label(__('monitoring.resource.columns.integrity'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'clean' => 'success',
                        'compromised' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('last_uptime_latency')
                    ->label(__('monitoring.resource.columns.latency'))
                    ->suffix(' ms')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_uptime_checked_at')
                    ->label(__('monitoring.resource.columns.last_checked'))
                    ->dateTime()
                    ->since()
                    ->placeholder('-'),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                Action::make('checkNow')
                    ->label(__('monitoring.resource.actions.check_now'))
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(function (MonitoredSite $record) {
                        CheckSiteUptimeJob::dispatchSync($record);
                        CheckSiteIntegrityJob::dispatchSync($record);
                        Notification::make()
                            ->title(__('monitoring.resource.actions.notifications.check_success'))
                            ->success()
                            ->send();
                    }),
                Action::make('recalibrate')
                    ->label(__('monitoring.resource.actions.recalibrate'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (MonitoredSite $record) {
                        try {
                            $record->recalibrateFromUrl();

                            Notification::make()
                                ->title(__('monitoring.resource.actions.notifications.recalibrate_success'))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                ActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonitoredSites::route('/'),
            'create' => Pages\CreateMonitoredSite::route('/create'),
            'edit' => Pages\EditMonitoredSite::route('/{record}/edit'),
        ];
    }
}
