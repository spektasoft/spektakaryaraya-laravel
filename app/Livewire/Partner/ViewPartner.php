<?php

namespace App\Livewire\Partner;

use App\Concerns\HasProjectsTable;
use App\Filament\Resources\PartnerResource;
use App\Models\Partner;
use Artesaos\SEOTools\Facades\SEOTools;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class ViewPartner extends Component implements HasForms, HasTable
{
    use HasProjectsTable;
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
