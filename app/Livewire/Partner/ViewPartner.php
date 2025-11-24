<?php

namespace App\Livewire\Partner;

use App\Concerns\HasProjectsTable;
use App\Filament\Resources\PartnerResource;
use App\Models\Partner;
use Artesaos\SEOTools\Facades\SEOTools;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class ViewPartner extends Component implements HasForms, HasInfolists, HasTable
{
    use HasProjectsTable;
    use InteractsWithForms;
    use InteractsWithInfolists;
    use InteractsWithTable;

    public Partner $partner;

    public function mount(Partner $partner): void
    {
        $description = Str::limit(strip_tags((string) $partner->description), 160, '…');

        SEOTools::setTitle((string) $partner->name);
        SEOTools::setDescription($description);

        $this->partner = $partner;
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [
            route('home') => __('navigation-menu.menu.home'),
            0 => trans_choice('partner.resource.model_label', 2),
            1 => $this->partner->name,
        ];

        return $breadcrumbs;
    }

    /**
     * @return array<Action>
     */
    public function getActions(): array
    {
        $actions = [];
        $actions[] = Action::make('edit')
            ->authorize(PartnerResource::canEdit($this->partner))
            ->label(__('partner.action.edit'))
            ->url(route('filament.admin.resources.partners.edit', $this->partner));

        return $actions;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->partner)
            ->schema([
                Grid::make([
                    'default' => 1,
                    'sm' => 16,
                ])->schema([
                    Section::make([
                        ImageEntry::make('logo.url')
                            ->hiddenLabel()
                            ->width('100%')
                            ->height('auto')
                            ->extraImgAttributes([
                                'class' => 'rounded-2xl w-full',
                            ])
                            ->visible(fn (Partner $record) => filled($record->logo)),
                        Entry::make('logo_placeholder')
                            ->hiddenLabel()
                            ->view('infolists.components.image-placeholder')
                            ->visible(fn (Partner $record) => blank($record->logo)),
                        Actions::make([
                            InfolistAction::make('visit')
                                ->label(__('partner.action.visit'))
                                ->icon('heroicon-m-globe-alt')
                                ->url(fn (Partner $record) => $record->url)
                                ->openUrlInNewTab()
                                ->button()
                                ->extraAttributes([
                                    'class' => 'w-full',
                                ]),
                        ])->alignCenter()->visible(fn (Partner $record) => filled($record->url)),
                    ])
                        ->compact()
                        ->columnSpan([
                            'default' => 1,
                            'sm' => 3,
                        ]),
                    Section::make([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->view('infolists.components.description-entry'),
                    ])
                        ->compact()
                        ->columnSpan([
                            'default' => 1,
                            'sm' => 13,
                        ]),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $this->configureProjectsTable($table)
            ->query(
                $this->partner->projects()->getQuery()
            );
    }

    public function render(): View
    {
        return view('livewire.partner.view-partner');
    }
}
