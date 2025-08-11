<?php

namespace App\Livewire\ServiceManagement;

use App\Exports\ServiceTimeLoggingExport;
use App\Models\ApartmentManagement;
use App\Models\ServiceClockInOut;
use App\Models\ServiceManagement;
use App\Models\User;
use App\Notifications\ServiceUserClockOutNotification;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportPagination\WithoutUrlPagination;
use Livewire\WithPagination;

class ServiceClockInOutTable extends Component
{
    use LivewireAlert, WithPagination, WithoutUrlPagination;
    protected $listeners = ['refreshAttendance' => 'mount'];

    public $search;
    public $attendance;
    public $showEditAttendanceModal = false;
    public $confirmDeleteAttendanceModal = false;
    public $clearFilterButton = false;
    public $showFilters = true;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $confirmSelectedDeleteAttendanceModal = false;
    public $dateRangeType;
    public $startDate;
    public $endDate;
    public $showServiceManagementModal = false;
    public $serviceManagementShow;

    public function mount()
    {
        $this->dateRangeType = 'today';
        $this->startDate = now()->startOfWeek()->format('m/d/Y');
        $this->endDate = now()->endOfWeek()->format('m/d/Y');
        $this->setDateRange();
    }

    public function setDateRange()
    {
        switch ($this->dateRangeType) {
            case 'today':
                $this->startDate = now()->startOfDay()->format('m/d/Y');
                $this->endDate = now()->startOfDay()->format('m/d/Y');
                break;

            case 'lastWeek':
                $this->startDate = now()->subWeek()->startOfWeek()->format('m/d/Y');
                $this->endDate = now()->subWeek()->endOfWeek()->format('m/d/Y');
                break;

            case 'last7Days':
                $this->startDate = now()->subDays(7)->format('m/d/Y');
                $this->endDate = now()->startOfDay()->format('m/d/Y');
                break;

            case 'currentMonth':
                $this->startDate = now()->startOfMonth()->format('m/d/Y');
                $this->endDate = now()->endOfMonth()->format('m/d/Y');
                break;

            case 'lastMonth':
                $this->startDate = now()->subMonth()->startOfMonth()->format('m/d/Y');
                $this->endDate = now()->subMonth()->endOfMonth()->format('m/d/Y');
                break;

            case 'currentYear':
                $this->startDate = now()->startOfYear()->format('m/d/Y');
                $this->endDate = now()->endOfYear()->format('m/d/Y');
                break;

            case 'lastYear':
                $this->startDate = now()->subYear()->startOfYear()->format('m/d/Y');
                $this->endDate = now()->subYear()->endOfYear()->format('m/d/Y');
                break;

            default:
                $this->startDate = now()->startOfWeek()->format('m/d/Y');
                $this->endDate = now()->endOfWeek()->format('m/d/Y');
                break;
        }
    }

    #[On('setStartDate')]
    public function setStartDate($start)
    {
        $this->startDate = $start;
    }

    #[On('setEndDate')]
    public function setEndDate($end)
    {
        $this->endDate = $end;
    }

    public function showEditAttendance($id)
    {
        $this->attendance = ServiceClockInOut::findOrFail($id);
        $this->showEditAttendanceModal = true;
    }

    #[On('hideEditAttendance')]
    public function hideEditAttendance()
    {
        $this->showEditAttendanceModal = false;
    }

    public function showDeleteAttendance($id)
    {
        $this->attendance = ServiceClockInOut::findOrFail($id);
        $this->confirmDeleteAttendanceModal = true;
    }

    public function deleteAttendance($id)
    {
        ServiceClockInOut::destroy($id);
        $this->confirmDeleteAttendanceModal = false;
        $this->attendance = '';

        $this->alert('success', __('messages.serviceClockInOutDeleted'));
    }

    public function showServiceManagement($id)
    {
        $this->serviceManagementShow = ServiceManagement::with('apartmentManagements')->findOrFail($id);
        $this->showServiceManagementModal = true;
    }

    #[On('hideShowServiceManagement')]
    public function hideShowServiceManagement()
    {
        $this->showServiceManagementModal = false;
        $this->js('window.location.reload()');
    }

    public function clockOut($id)
    {
        $attendance = ServiceClockInOut::findOrFail($id);

        if ($attendance->status == 'clock_in') {
            // Set clock-out time using current UTC time
            $attendance->clock_out_date = Carbon::now('UTC')->toDateString();
            $attendance->clock_out_time = Carbon::now('UTC')->format('H:i');
            $attendance->status = 'clock_out';
            $attendance->save();

            $this->alert('success', __('messages.clockOutSuccessfully'));
        }

        // Fetch the users associated with the service management
        $users = ApartmentManagement::whereHas('serviceManagements', function ($query) use ($attendance) {
            $query->where('service_management_id', $attendance->service_management_id);
        })->where(function ($q) {
            $q->orWhereHas('tenants')->orWhereNotNull('user_id');
        })->with('tenants')->get();

        // Gather unique user IDs (including tenant user IDs)
        $userIds = $users->pluck('user_id')->merge(
            $users->pluck('tenants.*.user_id')->flatten()
        )->unique();

        // Loop through each user and send a notification
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                // Ensure the service being passed is correctly formatted
                $user->notify(new ServiceUserClockOutNotification($attendance->service));
            }
        }
    }

    #[On('exportserviceTimeLogging')]
    public function exportserviceTimeLogging()
    {
        return (new ServiceTimeLoggingExport($this->search, $this->startDate, $this->endDate))->download('service-user-time-logging'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;
        $start = Carbon::createFromFormat('m/d/Y', $this->startDate)->startOfDay()->toDateTimeString();
        $end = Carbon::createFromFormat('m/d/Y', $this->endDate)->endOfDay()->toDateTimeString();
        $query = ServiceClockInOut::query()->with('service')->orderBy('clock_in_date', 'desc')->whereDate('clock_in_date', '>=', $start)->whereDate('clock_in_date', '<=', $end);
        
        if (!empty($this->search)) {
            $query->whereHas('service', function ($subQuery) {
                $subQuery->where('contact_person_name', 'like', '%' . $this->search . '%');
            });
            $this->clearFilterButton = true;
        }
                
        $loggedInUser = user()->id;

        if (!user_can('Show Service Time Logging') && isRole() != 'Guard') {
            $query->where(function ($subQuery) use ($loggedInUser) {
                $subQuery->whereHas('service.apartmentManagements', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser);
                })
                ->orWhereHas('service.apartmentManagements', function ($q) use ($loggedInUser) {
                    $q->whereHas('tenants', function ($q) use ($loggedInUser) {
                        $q->where('user_id', $loggedInUser)->where('apartment_tenant.status', 'current_resident');
                    });
                });
            });
        }

        $attendances = $query->paginate(10);

        return view('livewire.service-management.service-clock-in-out-table', [
            'attendances' => $attendances
        ]);
    }
}
