<?php

namespace App\Livewire\BookAmenity;

use App\Exports\BookAmenitiesExport;
use App\Models\Amenities;
use App\Models\BookAmenity;
use App\Models\User;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportPagination\WithoutUrlPagination;
use Livewire\WithPagination;

class BookAmenityTable extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    protected $listeners = ['refreshAmenity' => '$refresh'];

    public $search;
    public $amenity;
    public $amenitiesAll;
    public $showBookAmenityDetailModal = false;
    public $showEditBookAmenityModal = false;
    public $confirmDeleteBookAmenityModal = false;
    public $clearFilterButton = false;
    public $showFilters = false;
    public $filterUsers = [];
    public $filterAmenities = [];
    public $users;
    public $startDate;
    public $endDate;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $bookAmenitiesData;
    public $confirmSelectedDeleteBookAmenityModal = false;

    public function mount()
    {
        $this->users = User::all();
        $this->amenitiesAll = Amenities::all();
        $this->bookAmenitiesData = BookAmenity::get();
        $this->startDate = now()->format('m/d/Y');
        $this->endDate = now()->format('m/d/Y');
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

    public function showEditBookAmenity($id)
    {
        $this->amenity = BookAmenity::findOrFail($id);
        $this->showEditBookAmenityModal = true;
    }

    #[On('hideEditBookAmenity')]
    public function hideEditBookAmenity()
    {
        $this->showEditBookAmenityModal = false;
        $this->js('window.location.reload()');
    }

    public function showBookAmenityDetail($id)
    {
        $this->amenity = BookAmenity::findOrFail($id);
        $this->showBookAmenityDetailModal = true;
    }

    #[On('hideBookAmenityDetail')]
    public function hideBookAmenityDetail()
    {
        $this->showBookAmenityDetailModal = false;
    }

    public function showDeleteBookAmenity($id)
    {
        $this->amenity = BookAmenity::findOrFail($id);
        $this->confirmDeleteBookAmenityModal = true;
    }

    public function deleteBookAmenity($id)
    {
        $amenity = BookAmenity::findOrFail($id);

        $groupedRecords = BookAmenity::where('unique_id', $amenity->unique_id)->get();
        BookAmenity::whereIn('id', $groupedRecords->pluck('id'))->delete();        
        $this->confirmDeleteBookAmenityModal = false;
        $this->amenity = '';

        $this->alert('success', __('messages.bookAmenityDeleted'));
    }

    public function showSelectedDeleteBookAmenity()
    {
        $this->confirmSelectedDeleteBookAmenityModal = true;
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->bookAmenitiesData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function deleteSelected()
    {
        $selectedAmenities = BookAmenity::whereIn('id', $this->selected)->get();
        $uniqueIds = $selectedAmenities->pluck('unique_id')->unique();
        BookAmenity::whereIn('unique_id', $uniqueIds)->delete();
        
        $this->selected = [];
        $this->selectAll = false;   
        $this->showActions=false;
        $this->confirmSelectedDeleteBookAmenityModal=false;
        $this->alert('success', __('messages.bookAmenityDeleted'));
    }

    #[On('showBookAmenityFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterUsers = [];
        $this->filterAmenities = [];
        $this->search = '';
        $this->clearFilterButton = false;
    }

    #[On('resetBookAmenity')]
    public function resetBookAmenity()
    {
        $this->search = null;
        $this->startDate = now()->format('m/d/Y');
        $this->endDate = now()->format('m/d/Y');   
    }

    #[On('exportBookAmenity')]
    public function exportBookAmenity()
    {
        return (new BookAmenitiesExport($this->search, $this->startDate, $this->endDate, $this->filterUsers, $this->filterAmenities))->download('book-amenities-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;
        $query = BookAmenity::query()->with(['amenity', 'user'])->whereHas('amenity');
        if ($this->search != '') {
            $query->where(function ($q) {
                $q->whereHas('amenity', function ($q) {
                        $q->where('amenities_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterUsers)) {
            $query->whereIn('booked_by', $this->filterUsers);
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterAmenities)) {
            $query->whereHas('amenity', function ($q) {
                $q->whereIn('id', $this->filterAmenities);
            });
            $this->clearFilterButton = true;
        }

        if (!user_can('Show Book Amenity')) {
            $userId = user()->id;
            $query->where('booked_by', $userId);
        }
        if ($this->startDate && $this->endDate) {
            $start = Carbon::createFromFormat('m/d/Y', $this->startDate)->startOfDay()->toDateTimeString();
            $end = Carbon::createFromFormat('m/d/Y', $this->endDate)->endOfDay()->toDateTimeString();
            $query->whereBetween('Booking_date', [$start, $end]);
        }

        $paginatedUniqueIds = $query->selectRaw('unique_id, MAX(Booking_date) as max_booking_date')
            ->groupBy('unique_id')
            ->orderByDesc('max_booking_date')
            ->paginate(10);

        $amenities = BookAmenity::whereIn('unique_id', $paginatedUniqueIds->pluck('unique_id'))
            ->with(['amenity', 'user'])
            ->get()
            ->groupBy('unique_id');

        return view('livewire.book-amenity.book-amenity-table', [
            'amenities' => $amenities,
            'paginatedUniqueIds' => $paginatedUniqueIds
        ]);
    }
}
