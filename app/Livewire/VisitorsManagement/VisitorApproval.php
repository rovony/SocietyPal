<?php

namespace App\Livewire\VisitorsManagement;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class VisitorApproval extends Component
{
    use LivewireAlert;

    public $visitor;

    protected $rules = [
        'visitor.status' => 'in:allowed,not_allowed',
    ];

    public function approve()
    {
        $this->visitor->status = 'allowed';
        $this->visitor->save();
        $this->alert('success', __('messages.visitorAllowed'));
    }

    public function deny()
    {
        $this->visitor->status = 'not_allowed';
        $this->visitor->save();
        $this->alert('success', __('messages.visitorDenied'));
    }

    public function render()
    {
        return view('livewire.visitors-management.visitor-approval');
    }
}
