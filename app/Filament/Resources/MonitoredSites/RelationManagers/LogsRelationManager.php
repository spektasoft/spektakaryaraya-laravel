<?php

namespace App\Filament\Resources\MonitoredSites\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(__('monitoring.logs.type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'uptime' => 'info',
                        'integrity' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('monitoring.logs.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'up', 'clean' => 'success',
                        'down', 'compromised' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status_code')
                    ->label(__('monitoring.logs.http_status'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('latency')
                    ->label(__('monitoring.logs.latency'))
                    ->suffix(' ms')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('monitoring.logs.logged_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
