<?php

namespace App\Enums\MonitoredSite;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasLabel
{
    case Active = 'active';
    case Disabled = 'disabled';

    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            default => 'warning',
        };
    }

    public function getLabel(): string
    {
        return __('monitoring.resource.status.'.$this->value);
    }
}
