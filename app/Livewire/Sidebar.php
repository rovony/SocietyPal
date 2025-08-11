<?php

namespace App\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    protected function hasModule($module)
    {
        return in_array($module, society_role_modules());
    }

    protected function hasSocietyModule($module)
    {
        return in_array($module, society_modules());
    }

    public function render()
    {
        return view('livewire.sidebar');
    }
}
