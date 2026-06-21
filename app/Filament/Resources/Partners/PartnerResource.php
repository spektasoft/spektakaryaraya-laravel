<?php

namespace App\Filament\Resources\Partners;

use App\Filament\Actions\Tables\ReferenceAwareDeleteBulkAction;
use App\Filament\Forms\Components\CuratorEnabledRichEditor;
use App\Filament\Resources\Partners\Pages\CreatePartner;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Filament\Resources\Users\Utils\Creator;
use App\Filament\Tables\Columns\TranslatableTextColumn;
use App\Forms\Components\LocalesAwareTranslate;
use App\Models\Partner;
use App\Models\User;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

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
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('partner.resource.name'))
                                    ->required(),
                                CuratorEnabledRichEditor::make('description')
                                    ->label(__('partner.resource.description')),
                            ]),
                        Section::make()
                            ->schema([
                                TextInput::make('url')
                                    ->label(__('partner.resource.url'))
                                    ->url(),
                            ]),
                    ])->columnSpan([
                        'default' => 1,
                        'sm' => 4,
                    ]),
                    Group::make([
                        Section::make()
                            ->schema([
                                CuratorPicker::make('logo_id')
                                    ->label(__('partner.resource.logo'))
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
                    ->imageSize(40),
                TranslatableTextColumn::make('name')
                    ->label(__('partner.resource.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->label(__('partner.resource.url'))
                    ->searchable(),
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

    public static function getModelLabel(): string
    {
        return trans_choice('partner.resource.model_label', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('partner.resource.model_label', 2);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPartners::route('/'),
            'create' => CreatePartner::route('/create'),
            'edit' => EditPartner::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Administration');
    }

    /**
     * @return Builder<Partner>
     */
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<Partner> */
        $query = parent::getEloquentQuery();

        if (! static::canViewAll()) {
            $query->whereCreatorId(User::auth()?->id);
        }

        return $query->with(['creator']);
    }
}
