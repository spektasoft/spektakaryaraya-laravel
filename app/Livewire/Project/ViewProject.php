<?php

namespace App\Livewire\Project;

use App\Models\Partner;
use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ViewProject extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Project $project;

    public function mount(Project $project): void
    {
        $this->project = $project;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->project->partners()->getQuery()
            )
            ->columns([
                Tables\Columns\ImageColumn::make('logo.url')
                    ->label('Logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Partner Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('Website')
                    ->icon('heroicon-m-globe-alt')
                    ->url(fn (Partner $record) => $record->url)
                    ->openUrlInNewTab()
                    ->color('primary'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public function render(): View
    {
        return view('livewire.project.view-project');
    }
}
