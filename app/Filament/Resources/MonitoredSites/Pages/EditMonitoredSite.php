<?php

namespace App\Filament\Resources\MonitoredSites\Pages;

use App\Filament\Resources\MonitoredSites\MonitoredSiteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonitoredSite extends EditRecord
{
    protected static string $resource = MonitoredSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            MonitoredSiteResource::getCheckNowAction(),
            MonitoredSiteResource::getRecalibrateAction(),
            Actions\DeleteAction::make(),
        ];
    }
}
