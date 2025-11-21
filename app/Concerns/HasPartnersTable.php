<?php

namespace App\Concerns;

use Filament\Tables;
use Filament\Tables\Table;

trait HasPartnersTable
{
    public function configurePartnersTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\View::make('components.home.partners.table.index'),
            ])
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 4,
            ]);
    }
}
