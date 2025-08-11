<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CustomMenu;
use App\Models\CustomWebPage;

class NewCustomPages extends Component
{
    public $slug;

    public function render()
    {
        
        $customMenu = CustomWebPage::where('menu_slug', $this->slug)->where('is_active', 1)->firstOrFail();


        return view('livewire.new-custom-pages', [
            'customMenu' => $customMenu,
        ]);
    }

}
