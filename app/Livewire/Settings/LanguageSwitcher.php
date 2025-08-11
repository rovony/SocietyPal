<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;
use App\Models\LanguageSetting;
use App\Scopes\SocietyScope;

class LanguageSwitcher extends Component
{
    public function setLanguage($locale)
    {
        User::withoutGlobalScope(SocietyScope::class)->where('id', user()->id)->update(['locale' => $locale]);

        if (user()) {
            $language = LanguageSetting::where('language_code', $locale)->first();
            $isRtl = ($language->is_rtl == 1);
            session(['isRtl' => $isRtl]);
        }

        $this->js('window.location.reload()');

    }

    public function render()
    {
        $locale = auth()->user()->locale;

        $activeLanguage = LanguageSetting::where('language_code', $locale)->first();

        return view('livewire.settings.language-switcher', [
            'activeLanguage' => $activeLanguage,
        ]);
    }

}

