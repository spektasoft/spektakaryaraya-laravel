<?php

namespace App\Livewire\Home\Projects;

use App\Enums\Project\Status;
use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
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
            ->query(Project::query()->whereIn('status', [Status::Publish, Status::Archive])->orderBy('start_date', 'desc')->take(12))
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 4,
            ])
            ->columns([
                Tables\Columns\Layout\View::make('components.home.projects.table.index'),
            ])
            ->actions([
                // Actions are handled in the view component
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.home.projects.projects-table');
    }
}
