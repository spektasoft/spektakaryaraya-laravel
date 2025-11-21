<?php

namespace App\Livewire\Home\Projects;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProjectActions extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Project $project;

    public ?string $class = null;

    public function mount(Project $project, ?string $class = null): void
    {
        $this->project = $project;
        $this->class = $class;
    }

    public function viewAction(): Action
    {
        return Action::make('view')
            ->icon('heroicon-m-eye')
            ->url(route('projects.show', ['project' => $this->project]))
            ->extraAttributes(['wire:navigate' => '']);
    }

    public function render(): View
    {
        return view('livewire.home.projects.project-actions');
    }
}
