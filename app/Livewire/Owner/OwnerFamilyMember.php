<?php

namespace App\Livewire\Owner;

use App\Models\FamilyMember;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class OwnerFamilyMember extends Component
{
    use LivewireAlert;

    public $userId;
    public $user;
    public $familyMemberName = '';
    public $familyMembers = [];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::with(['familyMembers'])->findOrFail($this->userId);
        $this->familyMembers = $this->user->familyMembers()->get(['id', 'name'])->toArray();
    }

    public function addFamilyMember()
    {
        $this->validate([
            'familyMemberName' => 'required|string|max:255',
        ]);

        $familyMember = FamilyMember::create([
            'user_id' => $this->userId,
            'name' => $this->familyMemberName,
        ]);

        $this->familyMembers[] = [
            'id' => $familyMember->id,
            'name' => $this->familyMemberName,
        ];
        $this->familyMemberName = '';

        $this->alert('success', __('messages.familyMemberAdded'));
    }

    public function removeFamilyMember($familyMemberId)
    {
        $familyMember = FamilyMember::find($familyMemberId);
        $familyMember->delete();

        $this->familyMembers = array_filter($this->familyMembers, function ($member) use ($familyMemberId) {
            return $member['id'] !== $familyMemberId;
        });

        $this->familyMembers = array_values($this->familyMembers);
        $this->alert('success', __('messages.familyMemberDeleted'));
        
    }

    public function render()
    {
        return view('livewire.owner.owner-family-member');
    }
}
