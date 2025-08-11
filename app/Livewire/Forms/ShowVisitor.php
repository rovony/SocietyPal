<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ApartmentManagement;
use App\Models\VisitorManagement;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowVisitor extends Component
{
    use LivewireAlert, WithFileUploads;

    public $apartment;
    public $apartments;
    public $visitorId;
    public $visitor;
    public function mount()
    {
        $this->visitorId = $this->visitor->id;
        $this->visitor = VisitorManagement::with('apartment', 'user', 'visitorType')->findOrFail($this->visitorId);
        $this->apartments = $this->visitor->apartment_id;
        $this->apartment = ApartmentManagement::with('user')->find($this->apartments);

    }

    public function download()
    {
        $filePath = $this->visitor->visitor_photo;

        if($filePath){
            return Storage::disk(config('filesystems.default'))->download($filePath);
        }

    }

    public function render()
    {
        return view('livewire.forms.show-visitor');
    }
}
