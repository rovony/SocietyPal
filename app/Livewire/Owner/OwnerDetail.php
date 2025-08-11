<?php

namespace App\Livewire\Owner;

use App\Models\ApartmentManagement;
use App\Models\ParkingManagementSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ApartmentOwner;

class OwnerDetail extends Component
{
    use WithFileUploads ,LivewireAlert;

    public $user;
    public $userId;
    public $activeTab = 'apartment';
    public $apartments;
    public $parkings = [];
    public $tenants = [];
    protected $queryString = ['activeTab'];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::with(['familyMembers', 'documents', 'role'])->findOrFail($this->userId);
        $apartmentIds = ApartmentOwner::where('user_id', $this->userId)->pluck('apartment_id')->unique();
        $this->apartments = ApartmentManagement::with('floors', 'towers')
            ->whereIn('id', $apartmentIds)
            ->get();

        $this->tenants = Tenant::with(['apartments' => function ($query) use ($apartmentIds) {
            $query->whereIn('apartment_managements.id', $apartmentIds);
            }])->whereHas('apartments', function ($query) use ($apartmentIds) {
                $query->whereIn('apartment_managements.id', $apartmentIds);
            })->get();
        $this->parkings = ParkingManagementSetting::whereHas('apartmentManagement', function ($query) use ($apartmentIds) {
            $query->whereIn('apartment_parking.apartment_management_id', $apartmentIds);
        })->get();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function impersonate($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            $this->alert('error', 'No User found this society', [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            return true;
        }

        $admin = user();
        session()->flush();
        session()->forget('user');

        Auth::guard('web')->logout();
        session(['impersonate_user_id' => $admin->id]);
        session(['user' => $admin]);

        Auth::guard('web')->loginUsingId($user->id);

        session(['user' => $user]);

        return $this->redirect(route('dashboard'));
    }

    public function render()    
    {
        return view('livewire.owner.owner-detail');
    }
}
