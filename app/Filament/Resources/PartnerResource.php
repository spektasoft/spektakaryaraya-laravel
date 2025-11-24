<?php

namespace App\Filament\Resources;

use App\Filament\Actions\Tables\ReferenceAwareDeleteBulkAction;
use App\Filament\Resources\PartnerResource\Pages;
use App\Filament\Resources\UserResource\Utils\Creator;
use App\Filament\Tables\Columns\TranslatableTextColumn;
use App\Forms\Components\LocalesAwareTranslate;
use App\Models\Partner;
use App\Models\User;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;

class PartnerResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

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
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('partner.resource.name'))
                                    ->required(),
                                TiptapEditor::make('description')
                                    ->label(__('partner.resource.description')),
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('url')
                                    ->label(__('partner.resource.url'))
                                    ->url(),
                            ]),
                    ])->columnSpan([
                        'default' => 1,
                        'sm' => 4,
                    ]),
                    Forms\Components\Group::make([
                        Forms\Components\Section::make()
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
                    ->size(40),
                TranslatableTextColumn::make('name')
                    ->label(__('partner.resource.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label(__('partner.resource.url'))
                    ->searchable(),
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
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
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
