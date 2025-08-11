<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\CustomWebPage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DisableLanding extends Component
{
    use LivewireAlert;

    public $settings;
    public $disableLandingSite;
    public $landingType;

    public $landingSiteType;
    public $landingSiteUrl;
    public $facebook;
    public $instagram;
    public $twitter;
    public $metaKeyword;
    public $metaDescription;
    public $activeSetting = 'settings';
    public $menuName;
    public $menuSlug;
    public $menuContent;
    public $menuId;
    public $menuIdToDelete;
    public $showEditDynamicMenuModal = false;
    public $addDyanamicMenuModal = false;
    public $editMenuName;


    public function mount()
    {
        $this->landingType = $this->settings ? $this->settings->landing_type : false;
        $this->landingSiteType = $this->settings ? $this->settings->landing_site_type : '';
        $this->landingSiteUrl = $this->settings ? $this->settings->landing_site_url : '';
        $this->disableLandingSite = (bool)$this->settings->disable_landing_site;
        $this->landingSiteType = $this->settings->landing_site_type;
        $this->landingSiteUrl = $this->settings->landing_site_url;
        $this->facebook = $this->settings->facebook_link;
        $this->instagram = $this->settings->instagram_link;
        $this->twitter = $this->settings->twitter_link;
        $this->metaKeyword = $this->settings->meta_keyword;
        $this->metaDescription = $this->settings->meta_description;
    }

    public function submitForm()
    {

        $this->validate([
            'landingSiteUrl' => [
                'required_if:landingSiteType,custom',
                'nullable',
                'url',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $host = parse_url($value, PHP_URL_HOST);
                        $appUrl = parse_url(config('app.url'), PHP_URL_HOST);

                        if ($host === $appUrl) {
                            $fail(__('messages.cannotUseSelfDomain'));
                        }
                    }
                }
            ],
        ]);

        $this->settings->disable_landing_site = $this->disableLandingSite;
        $this->settings->landing_type = $this->landingType;
        $this->settings->landing_site_type = $this->landingSiteType;
        $this->settings->landing_site_url = $this->landingSiteUrl;
        $this->settings->facebook_link = $this->facebook;
        $this->settings->instagram_link = $this->instagram;
        $this->settings->twitter_link = $this->twitter;
        $this->settings->meta_keyword = $this->metaKeyword;
        $this->settings->meta_description = $this->metaDescription;
        $this->settings->save();

        cache()->forget('global_setting');

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }


    public function showEditDynamicMenu($id)
    {
        $this->menuId = $id;
        $this->showEditDynamicMenuModal = true;
    }

    public function showAddDynamicMenu()
    {
        $this->addDyanamicMenuModal = true;
    }

    #[On('closeAddDynamicMenu')]
    public function closeAddDynamicMenu()
    {
        $this->addDyanamicMenuModal = false;
        $this->js('window.location.reload()');

    }

    #[On('closeEditDynamicMenu')]
    public function closeEditDynamicMenu()
    {
        $this->showEditDynamicMenuModal = false;
        $this->js('window.location.reload()');
    }

    public function confirmDeleteMenu($menuId)
    {
        $this->menuIdToDelete = $menuId;
    }

    public function deleteMenu()
    {
        if ($this->menuIdToDelete) {
            $menu = CustomWebPage::find($this->menuIdToDelete);

            if ($menu) {
                $menu->delete();

                $this->menuIdToDelete = null;

                $this->alert('success', __('messages.settingsUpdated'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
            }
        }

        $this->dispatch('$refresh');
    }

    public function toggleMenuStatus($menuId)
    {
        $menu = CustomWebPage::findOrFail($menuId);
        $menu->is_active = !$menu->is_active;
        $menu->save();

        $this->dispatch('$refresh');

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }


    public function render()
    {
        return view('livewire.forms.disable-landing', [
            'customMenu' => CustomWebPage::paginate(10),
        ]);
    }
}
