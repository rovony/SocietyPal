<?php

namespace App\Livewire\Society;

use App\Models\Society;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class StopImpersonateSociety extends Component
{
    public function stopImpersonate()
    {
        $userId = session('impersonate_user_id');
        $societyId = session('impersonate_society_id');
        $society = Society::findOrFail($societyId);
        session()->flush();
        Auth::guard('web')->logout();
        session(['stop_impersonate' => $userId]);

        Auth::guard('web')->loginUsingId($userId);

        $redirect = route('superadmin.society.show', $society->slug);

        return $this->redirect($redirect);
    }

    public function render()
    {
        return view('livewire.society.stop-impersonate-society');
    }
}
