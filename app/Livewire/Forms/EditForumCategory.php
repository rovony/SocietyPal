<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\SocietyForumCategory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Blade;

class EditForumCategory extends Component
{
    use LivewireAlert;

    public $category;
    public $name;
    public $categoryId;
    public $selectedIcon;
    public array $icons = [];
    public string $search = '';
    public bool $showDropdown = false;

    public function mount()
    {
        $this->categoryId = $this->category->id;
        $this->name = $this->category->name;
        $this->selectedIcon = $this->category->icon;

        $this->icons = collect(Blade::getClassComponentAliases())
            ->keys()
            ->filter(fn($name) => Str::startsWith($name, 'heroicon-o-'))
            ->map(fn($name) => Str::after($name, 'heroicon-o-'))
            ->sort()
            ->values()
            ->toArray();
    }

    public function submitForm()
    {
        $this->validate([
            'name' => 'required|unique:society_forum_category,name,' . $this->categoryId . ',id,society_id,' . society()->id,
            'selectedIcon' => 'required',
        ]);

        $svgPath = base_path("vendor/blade-ui-kit/blade-heroicons/resources/svg/c-{$this->selectedIcon}.svg");

        $iconSize = 30;
        $iconSvg = File::exists($svgPath)
            ? preg_replace(
                '/<svg\b/',
                "<svg width=\"{$iconSize}\" height=\"{$iconSize}\" class=\"svg icon bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6\"",
                preg_replace('/(width|height)="\d+(\.\d+)?"/', '', File::get($svgPath)),
                1
            )
            : null;

        $this->category = SocietyForumCategory::findOrFail($this->categoryId);
        $this->category->name = $this->name;
        $this->category->icon = $this->selectedIcon;
        $this->category->image = $iconSvg;
        $this->category->save();

        $this->alert('success', __('messages.updatedSuccessfully'));
        $this->dispatch('hideEditForumCategory');
    }

    public function selectIcon($icon)
    {
        $this->selectedIcon = $icon;
        $this->showDropdown = false;
    }

    public function render()
    {
        return view('livewire.forms.edit-forum-category');
    }
}
