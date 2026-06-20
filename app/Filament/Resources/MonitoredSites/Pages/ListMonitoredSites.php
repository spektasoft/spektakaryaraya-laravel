<?php

namespace App\Filament\Resources\MonitoredSites\Pages;

use App\Filament\Resources\MonitoredSites\MonitoredSiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonitoredSites extends ListRecords
{
    protected static string $resource = MonitoredSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
