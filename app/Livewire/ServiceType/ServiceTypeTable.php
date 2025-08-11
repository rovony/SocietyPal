<?php

namespace App\Livewire\ServiceType;

use App\Models\ServiceType;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class ServiceTypeTable extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshServices' => 'mount'];

    public $services;
    public $service;
    public $showEditServiceModal = false;
    public $confirmDeleteServiceModal = false;

    public function mount()
    {
        $this->services = ServiceType::get();
    }
    
    public function render()
    {
        return view('livewire.service-type.service-type-table');
    }
}
