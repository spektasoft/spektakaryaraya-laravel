<?php

namespace App\Livewire\Project;

use App\Concerns\HasPartnersTable;
use App\Enums\Project\Status;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Artesaos\SEOTools\Facades\SEOTools;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;

class ViewProject extends Component implements HasActions, HasForms, HasInfolists, HasTable
{
    use HasPartnersTable;
    use InteractsWithActions;
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

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->project)
            ->schema([
                Grid::make([
                    'default' => 1,
                    'sm' => 16,
                ])->schema([
                    Section::make([
                        ImageEntry::make('logo.url')
                            ->hiddenLabel()
                            ->width('100%')
                            ->height('auto')
                            ->extraImgAttributes([
                                'class' => 'rounded-2xl w-full',
                            ])
                            ->visible(fn (Project $record) => filled($record->logo)),
                        Entry::make('logo_placeholder')
                            ->hiddenLabel()
                            ->view('infolists.components.image-placeholder')
                            ->visible(fn (Project $record) => blank($record->logo)),
                        Actions::make([
                            Action::make('visit')
                                ->label(__('project.action.visit'))
                                ->hidden(fn (Project $record) => $record->url === null)
                                ->icon('heroicon-m-globe-alt')
                                ->url(fn (Project $record) => $record->url)
                                ->openUrlInNewTab()
                                ->button()
                                ->extraAttributes([
                                    'class' => 'w-full',
                                ]),
                        ])->alignCenter()->visible(fn (Project $record) => filled($record->url)),
                    ])
                        ->compact()
                        ->columnSpan([
                            'default' => 1,
                            'sm' => 3,
                        ]),
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
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->view('infolists.components.description-entry'),
                    ])
                        ->compact()
                        ->columnSpan([
                            'default' => 1,
                            'sm' => 13,
                        ]),
                ]),
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
