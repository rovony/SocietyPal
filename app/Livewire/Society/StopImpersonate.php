<?php

namespace App\Livewire\Society;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StopImpersonate extends Component
{

    public function stopImpersonate()
    {
        $adminId = session('impersonate_user_id');
        $user = User::findOrFail($adminId);
        session()->flush();
        Auth::guard('web')->logout();

        session(['stop_impersonate' => $adminId]);
        Auth::guard('web')->loginUsingId($adminId);

        $redirect = route('dashboard');

        return $this->redirect($redirect);
    }
    
    public function render()
    {
        return view('livewire.society.stop-impersonate');
    }
}
