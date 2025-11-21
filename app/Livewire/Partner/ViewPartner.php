<?php

namespace App\Livewire\Partner;

use App\Models\Partner;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ViewPartner extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Partner $partner;

    public function mount(Partner $partner): void
    {
        $this->partner = $partner;
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
