<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SocietyForumCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SocietyForumSetting extends Component
{
    use LivewireAlert;

    public $categories;
    public $category;
    public $showEditForumCategoryModal = false;
    public $showAddForumCategoryModal = false;
    public $confirmDeleteForumCategoryModal = false;

    protected $listeners = ['refreshForumCategories' => 'mount'];
    public function mount()
    {
        $this->categories = SocietyForumCategory::all();
    }

    public function showAddForumCategory()
    {
        $this->dispatch('resetForm');
        $this->showAddForumCategoryModal = true;
    }

    public function showEditForumCategory($id)
    {
        $this->category = SocietyForumCategory::findOrFail($id);
        $this->showEditForumCategoryModal = true;
    }

    public function showDeleteForumCategory($id)
    {
        $this->category = SocietyForumCategory::findOrFail($id);
        $this->confirmDeleteForumCategoryModal = true;
    }

    public function deleteForumCategory($id)
    {
        SocietyForumCategory::destroy($id);

        $this->confirmDeleteForumCategoryModal = false;

        $this->category= '';
        $this->dispatch('refreshForumCategories');

        $this->alert('success', __('messages.deleteSuccess'));
    }

    #[On('hideEditForumCategory')]
    public function hideEditForumCategory()
    {
        $this->showEditForumCategoryModal = false;
        $this->dispatch('refreshForumCategories');

    }

    #[On('hideAddForumCategory')]
    public function hideAddForumCategory()
    {
        $this->showAddForumCategoryModal = false;
        $this->dispatch('refreshForumCategories');

    }
    public function render()
    {
        return view('livewire.settings.society-forum-setting');
    }
}
