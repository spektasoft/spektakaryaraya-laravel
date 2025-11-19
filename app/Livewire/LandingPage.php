<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class LandingPage extends Component
{
    public function render(): View
    {
        return view('livewire.landing-page');
    }
}
