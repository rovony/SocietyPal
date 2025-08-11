<?php

namespace App\Livewire\Dashboard;

use App\Models\ApartmentManagement;
use App\Models\ServiceClockInOut;
use App\Models\User;
use App\Notifications\ServiceUserClockOutNotification;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportPagination\WithoutUrlPagination;
use Livewire\WithPagination;

class TodayServiceClockInOut extends Component
{
    use LivewireAlert, WithPagination, WithoutUrlPagination;
    public $showAddAttendance = false;

    #[On('hideAddAttendance')]
    public function hideAddAttendance()
    {
        $this->showAddAttendance = false;
        $this->js('window.location.reload()');
    }

    public function clockOut($id)
    {
        $attendance = ServiceClockInOut::findOrFail($id);

        if ($attendance->status == 'clock_in') {
            $attendance->clock_out_date = Carbon::now('UTC')->toDateString();
            $attendance->clock_out_time = Carbon::now('UTC')->format('H:i');
            $attendance->status = 'clock_out';
            $attendance->save();

            $this->alert('success', __('messages.clockOutSuccessfully'));
        }
        $users = ApartmentManagement::whereHas('serviceManagements', function ($query) use ($attendance) {
            $query->where('service_management_id', $attendance->service_management_id);
        })->where(function ($q) {
            $q->orWhereHas('tenants')->orWhereNotNull('user_id');
        })->with('tenants')->get();

        $userIds = $users->pluck('user_id')->merge(
            $users->pluck('tenants.*.user_id')->flatten()
        )->unique();

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->notify(new ServiceUserClockOutNotification($attendance->service));
            }
        }
    }
    public function render()
    {
        $query = ServiceClockInOut::with('service')->whereDate('clock_in_date', now()->format('Y-m-d'))->orderBy('created_at', 'desc')->take(5)->get();
        return view('livewire.dashboard.today-service-clock-in-out', [
            'attendances' => $query
        ]);
    }
}
