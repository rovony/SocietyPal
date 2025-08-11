<?php

namespace App\Livewire\Notice;

use App\Exports\NoticesExport;
use App\Models\Notice;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use App\Models\Role;

class NoticeTable extends Component
{
    use LivewireAlert;

    public $search;
    public $notice;
    public $roles;
    public $showEditNoticeModal = false;
    public $showNoticeDetailModal = false;
    public $confirmDeleteNoticeModal = false;
    public $clearFilterButton = false;
    public $showFilters = false;
    public $filterRoles = [];
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $noticesData;
    public $confirmSelectedDeleteNoticeModal = false;

    public function mount()
    {
        $this->noticesData = Notice::get();
    }

    public function showEditNotice($id)
    {
        $this->notice = Notice::with('roles')->findOrFail($id);
        $this->showEditNoticeModal = true;
    }

    #[On('hideEditNotice')]
    public function hideEditNotice()
    {
        $this->showEditNoticeModal = false;
    }

    public function showNoticeDetail($id)
    {
        $this->notice = Notice::findOrFail($id);
        $this->showNoticeDetailModal = true;
    }

    #[On('hideNoticeDetail')]
    public function hideNoticeDetail()
    {
        $this->showNoticeDetailModal = false;
    }

    public function showDeleteNotice($id)
    {
        $this->notice = Notice::findOrFail($id);
        $this->confirmDeleteNoticeModal = true;
    }

    public function deleteNotice($id)
    {
        Notice::destroy($id);
        $this->confirmDeleteNoticeModal = false;
        $this->notice = '';

        $this->alert('success', __('messages.noticeDeleted'));
    }

    public function showSelectedDeleteNotice()
    {
        $this->confirmSelectedDeleteNoticeModal = true;
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->noticesData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function deleteSelected()
    {
        Notice::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions = false;
        $this->confirmSelectedDeleteNoticeModal = false;
        $this->alert('success', __('messages.noticeDeleted'));
    }


    #[On('showNoticeFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterRoles = [];
        $this->search = '';
        $this->clearFilterButton = false;
    }

    #[On('exportNotice')]
    public function exportNotice()
    {
        return (new NoticesExport($this->search, $this->filterRoles))->download('notices-' . now()->toDateTimeString() . '.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $this->roles = Role::all();

        $query = Notice::query();

        if ($this->search != '') {
            $query = $query->where('title', 'like', '%' . $this->search . '%');
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterRoles)) {
            $query->whereHas('noticeRoles', function ($query) {
                $query->whereIn('role_id', $this->filterRoles);
            });
            $this->clearFilterButton = true;
        }

        if (user_can('Show Notice Board')) {
            $notices = $query->paginate(10);
        } else {
            $userRoles = user()->roles->pluck('id')->toArray();

            $notices = $query->where(function ($q) use ($userRoles) {
                $q->whereHas('roles', function ($query) use ($userRoles) {
                    $query->whereIn('roles.id', $userRoles); // Use 'roles.id' to avoid ambiguity
                });
            })->paginate(10);
        }
        return view('livewire.notice.notice-table', [
            'notices' => $notices
        ]);
    }
}
