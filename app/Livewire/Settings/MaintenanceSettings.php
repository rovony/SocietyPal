<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Apartment;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class MaintenanceSettings extends Component
{

    use LivewireAlert;

    public $maintenanceSetting;
    public $costType;
    public $unitName;
    public $setValue;
    public $index;
    public $apartments = [];
    public $maintenance_value = [];

    public function mount()
    {
        $this->costType = $this->maintenanceSetting->cost_type;
        $this->unitName = $this->maintenanceSetting->unit_name;
        $this->setValue = $this->maintenanceSetting->set_value;
        $this->apartments = Apartment::all()->toArray();
    }

    public function updateApartment($index)
    {
        $apartment = Apartment::find($this->apartments[$index]['id']);

        $apartment->maintenance_value = $this->apartments[$index]['maintenance_value'];
        $apartment->save();
    }

    public function updateUnitType()
    {
        $this->validate([
            'unitName' => 'required|string',
            'setValue' => 'required|numeric|min:0',
        ]);

        $this->maintenanceSetting->unit_name = $this->unitName;
        $this->maintenanceSetting->set_value = $this->setValue;
        $this->maintenanceSetting->save();

        $this->alert('success', __('modules.settings.maintenanceSettingsUpdated'));
    }

    public function submitForm()
    {
        $costType = '';
        $validateRules = [
            'costType' => 'required',
        ];

        if ($this->costType == 'unitType'){
            $validateRules['unitName'] = 'required';
            $validateRules['setValue'] = 'required';

            $this->validate($validateRules);
        }

        foreach ($this->apartments as $apartmentData)
        {
            if ($this->costType == 'fixedValue')
            {
                $messages = [];

                foreach ($this->apartments as $index => $apartment)
                    {

                            $validateRules["apartments.$index.maintenance_value"] = 'required|numeric|min:1';
                            $messages["apartments.$index.maintenance_value.required"] =  __('messages.maintenanceValidationMessage');
                            $messages["apartments.$index.maintenance_value.required"] =  __('messages.maintenanceValidationMessage');
                            $messages["apartments.$index.maintenance_value.required"] =  __('messages.maintenanceValidationMessage');
                    }
                    $this->validate($validateRules,$messages);
            }
            

            $apartment = Apartment::find($apartmentData['id']);
            $apartment->maintenance_value = $apartmentData['maintenance_value'];
            $apartment->save();
        }

        $this->maintenanceSetting->cost_type = $this->costType;
        $this->maintenanceSetting->unit_name = $this->unitName;

        if ($this->costType == 'unitType') {
            $this->maintenanceSetting->set_value = $this->setValue;
        }
        $this->maintenanceSetting->save();

        $this->dispatch('settingsUpdated');

        $this->alert('success', __('modules.settings.maintenanceSettingsUpdated'));
    }


    public function render()
    {
        return view('livewire.settings.maintenance-settings');
    }
}
