<?php

namespace App\Livewire\Home;

use App\Enums\Project\Status;
use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProjectsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Project::query()->where('status', Status::Publish)->orderBy('start_date', 'desc')->take(12))
            ->columns([
                Stack::make([
                    Tables\Columns\ImageColumn::make('logo.path')
                        ->label(__('project.resource.logo'))
                        ->height(150)
                        ->width('100%')
                        ->extraImgAttributes(['class' => 'object-contain h-48 w-full bg-gray-50 dark:bg-gray-800 rounded-t-xl']),
                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->weight('bold')
                            ->size('lg')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('description')
                            ->limit(100)
                            ->html()
                            ->color('gray'),
                    ])->space(2)->extraAttributes(['class' => 'p-4']),
                ])->space(0)->extraAttributes(['class' => 'bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden']),
            ])
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 4,
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.home.projects-table');
    }
}
