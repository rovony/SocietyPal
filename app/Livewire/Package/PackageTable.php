<?php

namespace App\Livewire\Package;

use App\Models\Module;
use App\Models\Package;
use Livewire\Component;;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class PackageTable extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $search;
    public $package;
    public $roles;
    public $showEditPackageModal = false;
    public $confirmDeletePackageModal = false;
    public $allModules;
    public $packageDelete;
    public $additionalFeatures;

    protected $listeners = ['refreshPackages' => '$refresh'];

    public function mount()
    {
        $this->allModules = Module::all();
        $this->additionalFeatures = Package::ADDITIONAL_FEATURES;
    }

    public function showDeletePackage($id)
    {
        $this->packageDelete = Package::findOrFail($id);
        $this->confirmDeletePackageModal = true;
    }

    public function deletePackage($id)
    {
        Package::destroy($id);

        $this->confirmDeletePackageModal = false;

        $this->reset('packageDelete');
        $this->alert('success', __('messages.packageDeleted'));

    }

    public function render()
    {
        $query = Package::with('modules')
            ->where(function($q) {
            $q->where('package_name', 'like', '%'.$this->search.'%')
            ->orWhere('package_type', 'like', '%'.$this->search.'%');
            })
            ->paginate(20);

        return view('livewire.package.package-table', [
            'packages' => $query
        ]);
    }

}
