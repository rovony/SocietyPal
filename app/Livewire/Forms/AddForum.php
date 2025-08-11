<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\Forum;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;
use App\Models\Role;
use App\Models\User;
use App\Helper\Files;
use App\Models\SocietyForumCategory;
use Carbon\Carbon;
use App\Notifications\ForumCreatedNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\ForumFile;

class AddForum extends Component
{
    use LivewireAlert, WithFileUploads;

    public $society_id, $category_id, $title, $description, $date;
    public $discussion_type = 'public';
    public $roles, $users, $categories;
    public $members = []; 
    public $userRoleWise = 'role-wise'; 
    public $selectedRoles = [];
    public $selectedUserNames = [];
    public $isOpen = false;
    public $files = [];

    public function mount()
    {
        $this->roles = Role::get();
        $this->users = User::get();
        $this->discussion_type = 'public';
        $this->categories = SocietyForumCategory::where('society_id', society()->id)->get();
    }

    public function submitForm()
    {
        $rules = [
            'category_id' => 'required|exists:society_forum_category,id',
            'title' => 'required|string|max:255',
            'discussion_type' => 'required',
            'files.*' => 'nullable|file|max:12288',
        ];
        
        if ($this->discussion_type === 'private') {
            if ($this->userRoleWise === 'role-wise') {
                $rules['selectedRoles'] = 'required|array|min:1';
            } elseif ($this->userRoleWise === 'user-wise') {
                $rules['selectedUserNames'] = 'required|array|min:1';
            }
        }
        
        $this->validate($rules);

        $forum = new Forum();
        $forum->society_id = $this->society_id;
        $forum->category_id = $this->category_id;
        $forum->title = $this->title;
        $forum->description = $this->description;
        $forum->discussion_type = $this->discussion_type;
        $forum->user_selection_type = $this->userRoleWise;
        $forum->created_by = auth()->id();
        $forum->date = Carbon::today();
        $forum->save();

        if (!empty($this->files)) {
            foreach ($this->files as $uploadedFile) {
                $filePath = Files::uploadLocalOrS3($uploadedFile, ForumFile::FILE_PATH . '/');
                ForumFile::create([
                    'forum_id' => $forum->id,
                    'file' => $filePath,
                ]);
            }
        }
        if ($this->discussion_type === 'private') {
            if ($this->userRoleWise === 'role-wise') {
                $userIds = User::whereHas('roles', function ($q) {
                    $q->whereIn('roles.id', $this->selectedRoles);
                })->pluck('id')->toArray();
            } elseif ($this->userRoleWise === 'user-wise') {
                $userIds = $this->selectedUserNames;
            }
        
            if (!empty($userIds)) {
                $forum->users()->attach($userIds);
                $users = User::whereIn('id', $userIds)->get();
                Notification::send($users, new ForumCreatedNotification($forum));
            }
        } else {
            $allUsers = User::all();
            Notification::send($allUsers, new ForumCreatedNotification($forum));
        }

        $this->dispatch('hideAddForum');
        $this->alert('success', __('messages.addedSuccessfully'));
        $this->reset(['category_id', 'title', 'description', 'userRoleWise', 'selectedRoles', 'selectedUserNames']);

    }

    public function updatedDiscussionType($value)
    {
        if ($value === 'public') {
            $this->selectedRoles = [];
            $this->selectedUserNames = [];
        }
    }

    public function toggleSelectType($users)
    {
        if (in_array($users['id'], $this->selectedUserNames)) {
            $this->selectedUserNames = array_filter($this->selectedUserNames, fn($id) => $id !== $users['id']);
        } else {
            $this->selectedUserNames[] = $users['id'];
        }
        $this->selectedUserNames = array_values($this->selectedUserNames);

    }

    public function render()
    {
        return view('livewire.forms.add-forum');
    }
}
