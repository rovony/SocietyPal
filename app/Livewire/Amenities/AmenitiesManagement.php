<?php

namespace App\Livewire\Amenities;

use App\Exports\AmenitiesExport;
use App\Models\Amenities;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AmenitiesManagement extends Component
{

    use LivewireAlert;

    protected $listeners = ['refreshAmenities' => 'mount'];

    public $amenitiesManagment;
    public $amenities;
    public $search;
    public $showEditAmenitiesModal = false;
    public $showAddAmenitiesModal = false;
    public $confirmDeleteAmenitiesModal = false;
    public $confirmSelectedDeleteAmenitiesModal = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $showFilters = true;
    public $filterStatuses = ['available'];
    public $filterBookingRequired = [];
    public $clearFilterButton = false;

    public function mount()
    {
        $this->amenities = Amenities::get();
    }

    public function showAddAmenities()
    {
        $this->dispatch('resetForm');
        $this->showAddAmenitiesModal = true;
    }

    public function showEditAmenities($id)
    {
        $this->amenitiesManagment = Amenities::findOrFail($id);
        $this->showEditAmenitiesModal = true;
    }

    public function showDeleteAmenities($id)
    {
        $this->amenitiesManagment = Amenities::findOrFail($id);
        $this->confirmDeleteAmenitiesModal = true;
    }

    public function deleteAmenities($id)
    {
        Amenities::destroy($id);

        $this->confirmDeleteAmenitiesModal = false;

        $this->amenitiesManagment= '';
        $this->dispatch('refreshAmenities');

        $this->alert('success', __('messages.amenitiesDeleted'));
    }

    #[On('hideEditAmenities')]
    public function hideEditAmenities()
    {
        $this->showEditAmenitiesModal = false;
        $this->js('window.location.reload()');
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->amenities->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;

    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function showSelectedDelete()
    {
        $this->confirmSelectedDeleteAmenitiesModal = true;
    }

    public function deleteSelected()
    {
        Amenities::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteAmenitiesModal = false;
        $this->alert('success', __('messages.amenitiesDeleted'));

    }

    #[On('showAmenitiesFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }
    public function clearFilters()
    {
        $this->filterStatuses = [];
        $this->filterBookingRequired = [];
        $this->search = '';
    }

    #[On('exportAmenities')]
    public function exportAmenities()
    {
        return (new AmenitiesExport($this->search, $this->filterStatuses, $this->filterBookingRequired))->download('amenities-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $query = Amenities::query();

        if (!empty($this->search)) {
            $this->clearFilterButton = true;
            $query->where('amenities_name', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->filterStatuses)) {
            $this->clearFilterButton = true;
            $query->whereIn('status', $this->filterStatuses);
        }

        if (!empty($this->filterBookingRequired)) {
            $this->clearFilterButton = true;
            $query->whereIn('booking_status', $this->filterBookingRequired);
        }

        $amenitiesData = $query->paginate(10);

        return view('livewire.amenities.amenities-management', [
            'amenitiesData' => $amenitiesData,
        ]);
    }

}
