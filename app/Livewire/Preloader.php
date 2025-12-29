<?php

namespace App\Livewire;

use Livewire\Component;

class Preloader extends Component
{
    public $isLoading = true;
    public function render()
    {
        $this->dispatch('preloader');
        return view('livewire.preloader');
    }

}
