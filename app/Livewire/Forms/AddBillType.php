<?php

namespace App\Livewire\Forms;

use App\Models\BillType;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class AddBillType extends Component
{
    use LivewireAlert;

    public $billTypeName;
    public $billTypeCategory;

    public function submitForm()
    {
        $this->validate([
            'billTypeName' => 'required|unique:bill_types,name,' . $this->billTypeName . ',id,bill_type_category,' . $this->billTypeCategory,
            'billTypeCategory' => 'required',
        ]);


        $billType = new BillType();
        $billType->name = $this->billTypeName;
        $billType->bill_type_category = $this->billTypeCategory;
        $billType->save();

        $this->dispatch('hideAddBillType');

        $this->alert('success', __('messages.billTypeAdded'));

    }
    public function render()
    {
        return view('livewire.forms.add-bill-type');
    }
}
