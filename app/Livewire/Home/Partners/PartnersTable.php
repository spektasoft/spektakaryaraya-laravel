<?php

namespace App\Livewire\Home\Partners;

use App\Concerns\HasPartnersTable;
use App\Models\Partner;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PartnersTable extends Component implements HasForms, HasTable
{
    use HasPartnersTable;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $this->configurePartnersTable($table)
            ->query(Partner::query()->orderBy('name', 'asc')->take(12))
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.home.partners.partners-table');
    }
}
