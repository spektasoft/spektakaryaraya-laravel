<?php

namespace App\Livewire\Home\Projects;

use App\Concerns\HasProjectsTable;
use App\Enums\Project\Status;
use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProjectsTable extends Component implements HasForms, HasTable
{
    use HasProjectsTable;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $this->configureProjectsTable($table)
            ->query(Project::query()->whereIn('status', [Status::Publish, Status::Archive])->orderBy('start_date', 'desc')->take(12))
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.home.projects.projects-table');
    }
}
