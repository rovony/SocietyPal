<?php

namespace App\Livewire\Forms;

use App\Models\ApartmentManagement;
use App\Models\ServiceClockInOut;
use App\Models\ServiceManagement;
use App\Models\ServiceType;
use App\Models\User;
use App\Notifications\ServiceUserClockInNotification;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class AddAttendance extends Component
{
    use LivewireAlert;

    public $service_type_id;
    public $service_management_id;
    public $clock_in_date;
    public $clock_in_time;
    public $clock_out_date;
    public $clock_out_time;
    public $status = 'clock_in';
    public $serviceTypes;
    public $serviceProviders = [];

    public function mount()
    {
        $this->serviceTypes = ServiceType::all();
        $this->clock_in_date = Carbon::now()->timezone(timezone())->format('Y-m-d');
        $this->clock_in_time = Carbon::now()->timezone(timezone())->format('H:i');
    }

    public function updatedServiceTypeId($value)
    {
        $this->serviceProviders = ServiceManagement::where('service_type_id', $value)
            ->where('daily_help', 1)
            ->where('status', 'available')
            ->get();
        $this->service_management_id = null;
    }

    public function SubmitForm()
    {
        $this->validate([
            'service_type_id' => 'required',
            'service_management_id' => 'required',
            'clock_in_date' => 'required|date|before_or_equal:today',
            'clock_in_time' => 'required|date_format:H:i',
        ], [
            'service_management_id.required' => __('messages.serviceProviderRequired'),
            'service_type_id.required' => __('messages.serviceValidationMessage'),
        ]);

        $existingAttendance = ServiceClockInOut::where('service_management_id', $this->service_management_id)->whereDate('clock_in_date', Carbon::today())->latest()->first();
        if ($existingAttendance) {
            $this->addError('service_management_id', __('messages.serviceValidationError'));
            return;
        }

        $attendance = ServiceClockInOut::create([
            'service_management_id' => $this->service_management_id,
            'added_by' => user()->id,
            'clock_in_date' => Carbon::parse($this->clock_in_date)->timezone(timezone())->setTimezone('UTC'),
            'clock_in_time' => Carbon::createFromFormat('H:i', $this->clock_in_time, timezone())->setTimezone('UTC')->format('H:i'),
            'status' => 'clock_in',
        ]);

        $users = ApartmentManagement::whereHas('serviceManagements.serviceType', function ($query) {
            $query->where('service_management_id', $this->service_management_id);
        })->where(function ($q) {
            $q->orWhereHas('tenants')->orWhereNotNull('user_id');
        })->with('tenants')->get();

        $userIds = $users->pluck('user_id')->merge(
            $users->pluck('tenants.*.user_id')->flatten()
        )->unique();


        foreach ($userIds as $userId) {
            $user = User::find($userId);

            if ($user) {
                $user->notify(new ServiceUserClockInNotification($attendance->service));
            }
        }
        $this->dispatch('hideAddAttendance');
        $this->alert('success', __('messages.serviceClockInOutAdded'));
    }


    public function render()
    {
        return view('livewire.forms.add-attendance');
    }
}
