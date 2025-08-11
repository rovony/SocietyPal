<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditBillType extends Component
{
    use LivewireAlert;

    public $billType;
    public $billTypeName;
    public $billTypeId;
    public $billTypeCategory;

    public function mount()
    {
        $this->billTypeName = $this->billType->name;
        $this->billTypeCategory = $this->billType->bill_type_category;
        $this->billTypeId = $this->billType->id;
    }

    public function submitForm()
    {
        $this->validate([
            'billTypeName' => [
                    'required',
                    Rule::unique('bill_types', 'name')
                        ->where('bill_type_category', $this->billTypeCategory)
                        ->where('society_id', society()->id)
                        ->ignore($this->billTypeId),
        ],

            'billTypeCategory' => 'required',
    ]);

        $this->billType->name = $this->billTypeName;
        $this->billType->bill_type_category = $this->billTypeCategory;
        $this->billType->save();

        $this->alert('success', __('messages.billTypeUpdated'));

        $this->dispatch('hideEditBillType');
    }

    public function render()
    {
        return view('livewire.forms.edit-bill-type');
    }
}
