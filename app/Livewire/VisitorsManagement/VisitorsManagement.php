<?php

namespace App\Livewire\VisitorsManagement;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Apartment;
use Livewire\Attributes\On;
use App\Exports\VisitorsExport;
use App\Models\VisitorManagement;
use App\Models\ApartmentManagement;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class VisitorsManagement extends Component
{

    use LivewireAlert;

    protected $listeners = ['refreshVisitor' => 'mount'];

    public $apartments;
    public $apartmentsManagement;
    public $search;
    public $visitor;
    public $visitorManagements;
    public $utilityBills;
    public $tower;
    public $currencySymbol;
    public $showEditVisitorModal = false;
    public $showVisitorModal = false;
    public $showAddVisitorModal = false;
    public $confirmDeleteVisitorModal = false;
    public $confirmSelectedDeleteVisitorModal = false;
    public $selectAll = false;
    public $selected = [];
    public $filterApartments = [];
    public $showActions = false;
    public $showFilters = false;
    public $clearFilterButton = false;
    public $dateRangeType;
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->apartments = Apartment::get();
        $this->apartmentsManagement = ApartmentManagement::get();
        $this->visitorManagements = VisitorManagement::get();
        $this->dateRangeType = 'last7Days';
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

    public function showAddVisitor()
    {
        $this->dispatch('resetForm');
        $this->showAddVisitorModal = true;
    }

    public function showEditVisitor($id)
    {
        $this->visitor = VisitorManagement::findOrFail($id);
        $this->showEditVisitorModal = true;
    }

    public function showVisitor($id)
    {
        $this->visitor = VisitorManagement::findOrFail($id);
        $this->showVisitorModal = true;
    }

    public function showDeleteVisitor($id)
    {
        $this->visitor = VisitorManagement::findOrFail($id);
        $this->confirmDeleteVisitorModal = true;
    }

    public function deleteVisitor($id)
    {
        VisitorManagement::destroy($id);

        $this->confirmDeleteVisitorModal = false;

        $this->visitor = '';
        $this->dispatch('refreshVisitor');

        $this->alert('success', __('messages.visitorDeleted'));
    }

    #[On('hideEditVisitor')]
    public function hideEditVisitor()
    {
        $this->showEditVisitorModal = false;
        $this->js('window.location.reload()');
    }

    #[On('hideShowVisitor')]
    public function hideShowVisitor()
    {
        $this->showVisitorModal = false;
        $this->dispatch('refreshVisitor');
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->visitorManagements->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function showSelectedDelete()
    {
        $this->confirmSelectedDeleteVisitorModal = true;
    }

    public function deleteSelected()
    {
        VisitorManagement::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions = false;
        $this->confirmSelectedDeleteVisitorModal = false;
        $this->alert('success', __('messages.visitorDeleted'));
    }

    #[On('showVisitorFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterApartments = [];
    }

    #[On('resetdates')]
    public function resetdates()
    {
        $this->mount();
    }

    #[On('exportVisitors')]
    public function exportVisitors()
    {
        return (new VisitorsExport($this->search, $this->startDate, $this->endDate, $this->filterApartments))->download('visitor-' . now()->toDateTimeString() . '.xlsx');
    }

    public function setVisitorStatus($status, $visitorId)
    {
        $visitor = VisitorManagement::find($visitorId);

        $visitor->status = $status;
        $visitor->save();
        $this->alert('success', __('messages.vistorStatusUpdated'));
    }

    public function canShowDropdown($item)
    {
        return isRole() == 'Admin' 
            || (
                user()->id == $item->apartment->user_id 
                && in_array($item->apartment->status, ['occupied', 'available_for_rent'])
            )
            || (
                $item->apartment->tenants->contains('user_id', user()->id)
                && $item->apartment->status == 'rented'
            );
    }

    public function render()
    {
        $this->clearFilterButton = false;
        $start = Carbon::createFromFormat('m/d/Y', $this->startDate)->startOfDay()->toDateTimeString();
        $end = Carbon::createFromFormat('m/d/Y', $this->endDate)->endOfDay()->toDateTimeString();
        $query = VisitorManagement::query()->with(['apartment', 'apartment.tenants', 'user'])->whereDate('date_of_visit', '>=', $start)->whereDate('date_of_visit', '<=', $end)->orderBy('date_of_visit', 'desc')->orderBy('in_time', 'desc');;

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $subQuery->whereHas('apartment', function ($q) {
                    $q->where('apartment_number', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                    ->orWhere('visitor_name', 'like', '%' . $this->search . '%');
            });
        }

        $loggedInUser = user()->id;

        if (!user_can('Show Visitors')) {
            $query->where(function ($subQuery) use ($loggedInUser) {
                $subQuery->whereHas('apartment', function ($q) use ($loggedInUser) {
                    $q->where('status', 'available_for_rent')->orWhere('status', 'occupied')->where('user_id', $loggedInUser);
                })
                ->orWhereHas('apartment.tenants', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser)
                      ->where('apartment_tenant.status', 'current_resident')
                      ->whereColumn('apartment_tenant.contract_start_date', '<=', 'visitors_management.date_of_visit')
                        ->whereColumn('apartment_tenant.contract_end_date', '>=', 'visitors_management.date_of_visit');
                })
                ->orWhere('added_by', $loggedInUser);
            });
        }
        if (!empty($this->filterApartments)) {
            $query->whereHas('apartment', function ($query) {
                $query->whereIn('apartment_number', $this->filterApartments);
            });
            $this->clearFilterButton = true;
        }

        $visitorData = $query->paginate(10);

        return view('livewire.visitors-management.visitors-management', [
            'visitorData' => $visitorData,
        ]);
    }
}
