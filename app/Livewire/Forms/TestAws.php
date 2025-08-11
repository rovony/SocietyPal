<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Helper\Files;
use Livewire\WithFileUploads;
use App\Helper\Reply;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;

class TestAws extends Component
{
    use WithFileUploads, LivewireAlert;
    public $file;
    public $fileUrl;
    public $showFileLink = false;
    public $errorMessage;
    public function awsTest()
    {
        $this->validate([
            'file' => 'required|mimes:png,jpg,jpeg,csv,txt,xlx,xls,pdf,zip|max:2048',
        ]);
        
        try {
            $filename = Files::uploadLocalOrS3($this->file, '/');
            $this->fileUrl = asset_url_local_s3($filename);
            $this->showFileLink = true;
            $this->alert('success', __('messages.fileUploadedSuccessfully'));
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    #[On('resetFileData')]
    public function resetFileData()
    {

        $this->file = '';
        $this->errorMessage = '';
        $this->showFileLink = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.forms.test-aws');
    }
}
