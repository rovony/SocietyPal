<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\VisitorTypeSettingsModel;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class VisitorTypeSettings extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshVisitorTypes' => 'mount'];

    public $visitorTypes;
    public $visitorType;
    public $showEditVisitorTypeModal = false;
    public $showAddVisitorTypeModal = false;
    public $confirmDeleteVisitorTypeModal = false;

    public function mount()
    {
        $this->visitorTypes = VisitorTypeSettingsModel::get();
    }

    public function showAddVisitorType()
    {
        $this->dispatch('resetForm');
        $this->showAddVisitorTypeModal = true;
    }

    public function showEditVisitorType($id)
    {
        $this->visitorType = VisitorTypeSettingsModel::findOrFail($id);
        $this->showEditVisitorTypeModal = true;
    }

    public function showDeleteVisitorType($id)
    {
        $this->visitorType = VisitorTypeSettingsModel::findOrFail($id);
        $this->confirmDeleteVisitorTypeModal = true;
    }

    public function deleteVisitorType($id)
    {
        VisitorTypeSettingsModel::destroy($id);

        $this->confirmDeleteVisitorTypeModal = false;

        $this->visitorType= '';
        $this->dispatch('refreshVisitorTypes');

        $this->alert('success', __('messages.visitorTypeDeleted'));
    }

    #[On('hideEditVisitorType')]
    public function hideEditVisitorType()
    {
        $this->showEditVisitorTypeModal = false;
        $this->dispatch('refreshVisitorTypes');
    }

    #[On('hideAddVisitorType')]
    public function hideAddVisitorType()
    {
        $this->showAddVisitorTypeModal = false;
        $this->dispatch('refreshVisitorTypes');
    }

    public function render()
    {
        return view('livewire.settings.visitor-type-settings');
    }
}
