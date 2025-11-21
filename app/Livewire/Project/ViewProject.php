<?php

namespace App\Livewire\Project;

use App\Concerns\HasPartnersTable;
use App\Enums\Project\Status;
use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Artesaos\SEOTools\Facades\SEOTools;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\Actions as InfolistActions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;

class ViewProject extends Component implements HasForms, HasInfolists, HasTable
{
    use HasPartnersTable;
    use InteractsWithForms;
    use InteractsWithInfolists;
    use InteractsWithTable;

    public Project $project;

    public function mount(Project $project): void
    {
        if (! in_array($project->status, [Status::Publish, Status::Archive]) && ! Gate::allows('update', $project)) {
            abort(404);
        }

        $description = Str::limit(strip_tags($project->description), 160, '…');

        SEOTools::setTitle($project->name);
        SEOTools::setDescription($description);

        $this->project = $project;
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [
            route('home') => __('navigation-menu.menu.home'),
            0 => trans_choice('project.resource.model_label', 2),
            1 => $this->project->name,
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
            ->authorize(ProjectResource::canEdit($this->project))
            ->label(__('project.action.edit'))
            ->url(route('filament.admin.resources.projects.edit', $this->project));

        return $actions;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->project)
            ->schema([
                Split::make([
                    Section::make([
                        TextEntry::make('start_date')
                            ->hiddenLabel()
                            ->alignLeft()
                            ->badge()
                            ->date('Y')
                            ->color(fn (Project $record) => match ($record->status) {
                                Status::Publish => 'primary',
                                Status::Archive => 'gray',
                                Status::Draft => 'warning',
                            }),
                        ImageEntry::make('logo.url')
                            ->hiddenLabel()
                            ->height('100%')
                            ->extraImgAttributes([
                                'class' => 'rounded-2xl',
                            ])
                            ->visible(fn (Project $record) => filled($record->logo)),
                        InfolistActions::make([
                            InfolistAction::make('visit')
                                ->label(fn (Project $record) => $record->url)
                                ->hidden(fn (Project $record) => $record->url === null)
                                ->icon('heroicon-m-globe-alt')
                                ->url(fn (Project $record) => $record->url)
                                ->openUrlInNewTab()
                                ->button(),
                        ])->alignCenter()->visible(fn (Project $record) => filled($record->url)),
                    ])
                        ->grow(false)
                        ->compact(),
                    Section::make([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->view('infolists.components.description-entry'),
                    ]),
                ])->from('md'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $this->configurePartnersTable($table)
            ->query(
                $this->project->partners()->getQuery()
            );
    }

    public function render(): View
    {
        return view('livewire.project.view-project');
    }
}
