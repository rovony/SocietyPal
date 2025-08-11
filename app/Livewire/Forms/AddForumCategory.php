<?php

namespace App\Livewire\Forms;

use App\Models\SocietyForumCategory;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;

class AddForumCategory extends Component
{
    use LivewireAlert;

    public $name;
    public $icon;
    public string $selectedIcon = '';
    public array $icons = [];
    public bool $showDropdown = false;
    public string $search = '';

    public function mount()
    {
        $allComponents = Blade::getClassComponentAliases();

        $this->icons = collect($allComponents)
            ->keys()
            ->filter(fn($name) => Str::startsWith($name, 'heroicon-o-'))
            ->map(fn($name) => Str::after($name, 'heroicon-o-'))
            ->sort()
            ->values()
            ->toArray();
    }

    public function submitForm()
    {
        $svgPath = base_path("vendor/blade-ui-kit/blade-heroicons/resources/svg/c-{$this->selectedIcon}.svg");

        $iconSize = 30;

        if (File::exists($svgPath)) {
            $iconSvg = File::get($svgPath);
            $iconSvg = preg_replace('/(width|height)="\d+(\.\d+)?"/', '', $iconSvg); // Remove existing
            $iconSvg = preg_replace(
            '/<svg\b/',
            "<svg width=\"{$iconSize}\" height=\"{$iconSize}\" class=\"svg icon bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6\"",
            $iconSvg,
            1
            );
        } else {
            $iconSvg = null;
        }

        $this->validate([
            'name' => 'required|unique:society_forum_category,name,NULL,id,society_id,' . society()->id,
            'selectedIcon' => 'required',
        ]);

        $category = new SocietyForumCategory();
        $category->name = $this->name;
        $category->icon = $this->selectedIcon;
        $category->image = $iconSvg;
        $category->save();

        $this->alert('success', __('messages.addedSuccessfully'));

        $this->dispatch('hideAddForumCategory');
    }

    public function selectIcon($icon)
    {
        $this->selectedIcon = $icon;
        $this->showDropdown = false;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['name', 'selectedIcon']);
    }
    public function render()
    {
        return view('livewire.forms.add-forum-category');
    }
}
