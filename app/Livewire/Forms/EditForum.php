<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Role;
use App\Models\User;
use App\Helper\Files;
use App\Models\SocietyForumCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Carbon\Carbon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\Forum;
use App\Models\ForumFile;

class EditForum extends Component
{
    use LivewireAlert, WithFileUploads;

    public $forum;
    public $forumId;
    public $society_id, $category_id, $title, $description, $date;
    public $discussion_type;
    public $roles, $users, $categories;
    public $members = [];
    public $userRoleWise = 'role-wise';
    public $selectedRoles = [];
    public $selectedUserNames = [];
    public $isOpen = false;
    public $files = [];
    public $existingFiles = [];

    public function mount()
    {
        $this->forumId = $this->forum->id;
        $this->society_id = $this->forum->society_id;
        $this->category_id = $this->forum->category_id;
        $this->title = $this->forum->title;
        $this->description = $this->forum->description;
        $this->discussion_type = $this->forum->discussion_type;
        $this->userRoleWise = $this->forum->user_selection_type;
        $this->date = $this->forum->date;

        $this->roles = Role::get();
        $this->users = User::get();
        $this->categories = SocietyForumCategory::where('society_id', society()->id)->get();

        if ($this->discussion_type === 'private') {
            $userIds = $this->forum->users()->pluck('users.id')->toArray();
            $this->selectedUserNames = $userIds;
            $roleIds = User::whereIn('id', $userIds)->with('roles')->get()->pluck('roles.*.id')->flatten()->unique()->toArray();
            $this->selectedRoles = $roleIds;
        }
        $this->existingFiles = $this->forum->files()->select('id', 'file')->get();

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


        $this->forum->category_id = $this->category_id;
        $this->forum->title = $this->title;
        $this->forum->description = $this->description;     
        $this->forum->discussion_type = $this->discussion_type;
        $this->forum->user_selection_type = $this->userRoleWise;
        $this->forum->date = Carbon::today();
        $this->forum->save();
        if (!empty($this->files)) {
            foreach ($this->files as $uploadedFile) {
                $filePath = Files::uploadLocalOrS3($uploadedFile, ForumFile::FILE_PATH . '/');
                ForumFile::create([
                    'forum_id' => $this->forum->id,
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

            $this->forum->users()->sync($userIds);
        } else {
            $this->forum->users()->detach();
        }

        $this->dispatch('hideEditForum');
        $this->alert('success', __('messages.updatedSuccessfully'));
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

    public function deleteFile($id)
    {
        $file = ForumFile::where('id', $id)
            ->where('forum_id', $this->forum->id)
            ->first();

        if ($file) {
            Files::deleteFile($file->file, ForumFile::FILE_PATH);
            $file->delete();
        }

        $this->existingFiles = ForumFile::where('forum_id', $this->forum->id)->select('id', 'file')->get();
    }

    public function render()
    {
        return view('livewire.forms.edit-forum');
    }
}
