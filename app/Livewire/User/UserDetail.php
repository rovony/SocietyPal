<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;


class UserDetail extends Component
{
    public $user;
    public $userId;

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::findOrFail($this->userId);
    }

    public function impersonate($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            $this->alert('error', 'No User found this society', [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            return true;
        }

        $admin = user();
        session()->flush();
        session()->forget('user');

        Auth::guard('web')->logout();
        session(['impersonate_user_id' => $admin->id]);
        session(['user' => $user]);
        session()->flush();
        session(['impersonate_user_id' => $admin->id]);

        Auth::guard('web')->loginUsingId($user->id);

        return $this->redirect(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.user.user-detail');
    }
}
