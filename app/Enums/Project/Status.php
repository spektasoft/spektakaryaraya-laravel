<?php

namespace App\Enums\Project;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case Publish = 'publish';
    case Archive = 'archive';

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'warning',
            self::Archive => 'gray',
            default => 'success',
        };
    }

    public function getLabel(): string
    {
        return __('project.resource.status.'.$this->value);
    }
}
