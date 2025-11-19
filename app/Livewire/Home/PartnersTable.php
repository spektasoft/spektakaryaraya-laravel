<?php

namespace App\Livewire\Home;

use App\Models\Partner;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PartnersTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Partner::query()->orderBy('name', 'asc'))
            ->columns([
                Stack::make([
                    Tables\Columns\ImageColumn::make('logo.path')
                        ->label('Logo')
                        ->height(100)
                        ->width('100%')
                        ->extraImgAttributes(['class' => 'object-contain h-32 w-full p-4']),
                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->weight('bold')
                            ->alignCenter()
                            ->searchable(),
                    ])->extraAttributes(['class' => 'pb-4 px-4']),
                ])->extraAttributes(['class' => 'bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10']),
            ])
            ->contentGrid([
                'sm' => 2,
                'md' => 3,
                'xl' => 4,
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.home.partners-table');
    }
}
