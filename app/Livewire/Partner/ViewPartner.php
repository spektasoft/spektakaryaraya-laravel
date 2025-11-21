<?php

namespace App\Livewire\Partner;

use App\Filament\Resources\PartnerResource;
use App\Models\Partner;
use Artesaos\SEOTools\Facades\SEOTools;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class ViewPartner extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
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

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->partner->projects()->getQuery()
            )
            ->columns([
                Tables\Columns\ImageColumn::make('logo.url')
                    ->label('Logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn ($record) => route('projects.show', $record))
                    ->icon('heroicon-m-eye'),
            ])
            ->bulkActions([
                //
            ]);
    }

    public function render(): View
    {
        return view('livewire.partner.view-partner');
    }
}
