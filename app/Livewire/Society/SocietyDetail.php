<?php

namespace App\Livewire\Society;

use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SocietyDetail extends Component
{
    use LivewireAlert;

    public $society;
    public $societyId;
    public $id;
    public $societyAdmin;
    public $showPasswordModal = false;
    public $password;
    public $slug;
    public $search;

    public function mount()
    {
        $this->society = Society::with('currency', 'societyPayment', 'societyPayment.package', 'societyPayment.package.currency')->where('slug', $this->slug)->firstOrFail();

        $this->societyAdmin = $this->society->users->sortBy('id')->first();
    }

    public function impersonate($societyId)
    {
        $admin = User::where('society_id', $societyId)->first();

        if (!$admin) {
            $this->alert('error', 'No admin found this society', [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            return true;
        }

        $user = user();
        session()->flush();
        session()->forget('user');

        Auth::logout();
        session(['impersonate_user_id' => $user->id]);
        session(['impersonate_society_id' => $societyId]);
        session(['user' => $admin]);

        Auth::loginUsingId($admin->id);

        session(['user' => auth()->user()]);

        return $this->redirect(route('dashboard'));
    }

    public function submitForm()
    {
        $this->validate([
            'password' => 'required'
        ]);

        $this->societyAdmin->password = $this->password;
        $this->societyAdmin->save();

        $this->alert('success', __('messages.profileUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->password = null;
        $this->showPasswordModal = false;
    }

    public function render()
    {
        return view('livewire.society.society-detail');
    }
}
