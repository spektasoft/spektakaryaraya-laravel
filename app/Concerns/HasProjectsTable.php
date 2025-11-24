<?php

namespace App\Concerns;

use Filament\Tables;
use Filament\Tables\Table;

trait HasProjectsTable
{
    public function configureProjectsTable(Table $table): Table
    {
        return $table
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 4,
            ])
            ->columns([
                Tables\Columns\Layout\View::make('components.home.projects.table.index'),
            ]);
    }
}
