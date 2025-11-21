<?php

namespace App\Livewire\Home\Partners;

use App\Models\Partner;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PartnerActions extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Partner $partner;

    public ?string $class = null;

    public function mount(Partner $partner, ?string $class = null): void
    {
        $this->partner = $partner;
        $this->class = $class;
    }

    public function viewAction(): Action
    {
        return Action::make('view')
            ->icon('heroicon-m-eye')
            ->url(route('partners.show', ['partner' => $this->partner]))
            ->extraAttributes(['wire:navigate' => '']);
    }

    public function render(): View
    {
        return view('livewire.home.partners.partner-actions');
    }
}
