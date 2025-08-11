<?php

namespace App\Livewire\Forms;

use App\Models\ApartmentManagement;
use App\Models\ServiceClockInOut;
use App\Models\ServiceManagement;
use App\Models\User;
use App\Notifications\ServiceUserClockOutNotification;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditAttendance extends Component
{
    use LivewireAlert;

    public $service_management_id;
    public $clock_in_date;
    public $clock_in_time;
    public $clock_out_date;
    public $clock_out_time;
    public $duration_minutes;
    public $status = 'clock_in';
    public $serviceProviders;
    public $attendance;
    public $attendanceId;

    public function mount()
    {
        $this->serviceProviders = ServiceManagement::where('daily_help', 1)->where('status', 'available')->get();
        $this->attendanceId = $this->attendance->id;
        $this->service_management_id = $this->attendance->service_management_id;

        $this->clock_in_date = Carbon::parse($this->attendance->clock_in_date)->timezone(timezone())->format('Y-m-d');
        $this->clock_in_time = Carbon::parse($this->attendance->clock_in_time)->timezone(timezone())->format('H:i');

        if ($this->attendance->clock_out_date && $this->attendance->clock_out_time) {
            $this->clock_out_date = Carbon::parse($this->attendance->clock_out_date)->timezone(timezone())->format('Y-m-d');
            $this->clock_out_time = Carbon::parse($this->attendance->clock_out_time)->timezone(timezone())->format('H:i');
        }

        $this->status = $this->attendance->status;
    }

    public function SubmitForm()
    {
        $this->validate([
            'service_management_id' => 'required',
            'clock_in_date' => 'required|date',
            'clock_in_time' => 'required',
            'clock_out_date' => 'nullable|date|after_or_equal:clock_in_date',
            'clock_out_time' => 'nullable',
        ], [
            'clock_out_date.after_or_equal' => __('messages.clockOutDateValidation'),
            'service_management_id.required' => __('messages.serviceProviderRequired'),
        ]);

        // Custom validation for the same date case
        if ($this->clock_out_date && $this->clock_out_date === $this->clock_in_date) {
            $clockInTime = Carbon::createFromFormat('H:i', $this->clock_in_time);
            $clockOutTime = Carbon::createFromFormat('H:i', $this->clock_out_time);
            
            if ($clockOutTime <= $clockInTime) {
                $this->addError('clock_out_time', __('messages.clockOutTimeValidation'));
                return;
            }
        }

        // Check if another attendance record exists for the same service management on the same day
        $existingAttendance = ServiceClockInOut::where('service_management_id', $this->service_management_id)
            ->whereDate('clock_in_date', Carbon::parse($this->clock_in_date)->format('Y-m-d'))
            ->where('id', '!=', $this->attendanceId)
            ->latest()->first();

        if ($existingAttendance) {
            $this->addError('service_management_id', __('messages.serviceValidationError'));
            return;
        }

        // Parse and store clock-in values correctly, ensuring the timezone is handled
        $this->attendance->service_management_id = $this->service_management_id;
        $this->attendance->clock_in_date = Carbon::parse($this->clock_in_date)->timezone(timezone())->setTimezone('UTC')->format('Y-m-d');
        $this->attendance->clock_in_time = Carbon::createFromFormat('H:i', $this->clock_in_time, timezone())->setTimezone('UTC')->format('H:i');

        // Handle clock-out date and time logic
        if ($this->clock_out_date && $this->clock_out_time) {
            // Set clock-out date and time with proper timezone handling
            $this->attendance->clock_out_date = Carbon::parse($this->clock_out_date)->timezone(timezone())->setTimezone('UTC')->format('Y-m-d');
            $this->attendance->clock_out_time = Carbon::createFromFormat('H:i', $this->clock_out_time, timezone())->setTimezone('UTC')->format('H:i');
            
            // Concatenate date and time correctly for comparison
            $clockIn = Carbon::parse("{$this->attendance->clock_in_date} {$this->attendance->clock_in_time}");
            $clockOut = Carbon::parse("{$this->attendance->clock_out_date} {$this->attendance->clock_out_time}");

            // Ensure clock-out time is greater than clock-in time
            if ($clockOut->greaterThan($clockIn)) {
                $this->attendance->duration_minutes = $clockIn->diffInMinutes($clockOut);
                $this->attendance->status = 'clock_out';
            } else {
                $this->addError('clock_out_time', __('messages.clockOutTimeValidation')); 
                return;
            }
        } else {
            // Handle the case where the user hasn't clocked out
            $this->attendance->status = 'clock_in';
            $this->attendance->clock_out_date = null;
            $this->attendance->clock_out_time = null;
            $this->attendance->duration_minutes = null;
        }

        // Save the attendance record
        $this->attendance->save();

        // Success message
        $this->alert('success', __('messages.serviceClockInOutUpdated'));   
        $this->dispatch('hideAddAttendance');
    }

    public function render()
    {
        return view('livewire.forms.edit-attendance');
    }
}
