<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;
use App\Models\OnboardingStep;
use Livewire\Attributes\On;

class OnboardingSteps extends Component
{
    public $showAddTower = false;
    public $showAddFloor = false;
    public $showAddApartment = false;
    public $showAddParking = false;

    #[On('towerAdded')]
    public function towerAdded()
    {
        $onboarding = OnboardingStep::first();
        $onboarding->add_tower_completed = 1;
        $onboarding->save();

        $this->showAddTower = false;
    }
    
    #[On('floorAdded')]
    public function floorAdded()
    {
        $onboarding = OnboardingStep::first();
        $onboarding->add_floor_completed = 1;
        $onboarding->save();

        $this->showAddFloor = false;
    }

    #[On('apartmentAdded')]
    public function apartmentAdded()
    {
        $onboarding = OnboardingStep::first();
        $onboarding->add_apartment_completed = 1;
        $onboarding->save();

        $this->showAddApartment = false;
    }

    #[On('parkingAdded')]
    public function parkingAdded()
    {
        $onboarding = OnboardingStep::first();
        $onboarding->add_parking_completed = 1;
        $onboarding->save();
    }

    public function showAddTowerForm()
    {
        $this->showAddTower = false;
        $this->showAddFloor = false;
        $this->showAddApartment = true;
        $this->showAddParking = false;
    }

    public function showAddFloorForm()
    {
        $this->showAddTower = false;
        $this->showAddFloor = true;
        $this->showAddApartment = false;
        $this->showAddParking = false;
    }

    public function showAddApartmentForm()
    {
        $this->showAddTower = true;
        $this->showAddFloor = false;
        $this->showAddApartment = false;
        $this->showAddParking = false;
    }

    public function showAddParkingForm()
    {
        $this->showAddTower = false;
        $this->showAddFloor = false;
        $this->showAddApartment = false;
        $this->showAddParking = true;
    }
    
    public function render()
    {
        $onboardingSteps = OnboardingStep::first();

        return view('livewire.onboarding.onboarding-steps', [
            'onboardingSteps' => $onboardingSteps
        ]);
    }
}
