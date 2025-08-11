<?php

namespace App\Livewire\Owner;

use App\Helper\Files;
use App\Models\OwnerDocument;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class OwnerDocuments extends Component
{
    use WithFileUploads, LivewireAlert;

    public $userId;
    public $user;
    public $documentName = '';
    public $documentFile;
    public $documents = [];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::with(['documents'])->findOrFail($this->userId);
        $this->documents = $this->user->documents()->get(['id', 'document_name', 'document'])->toArray();
    }
    
    public function addDocument()
    {
        $this->validate([
            'documentName' => 'required|string|max:255',
            'documentFile' => 'required|file|max:12288', // max 1MB
        ]);

        $documentPath = Files::uploadLocalOrS3($this->documentFile, '/');

        $document = OwnerDocument::create([
            'user_id' => $this->userId,
            'document_name' => $this->documentName,
            'document' => $documentPath,
        ]);

        $this->documents[] = [
            'id' => $document->id,
            'document_name' => $this->documentName,
            'document' => $documentPath,
        ];

        $this->documentName = '';
        $this->documentFile = null;
        $this->alert('success', __('messages.documentAdded'));
    }

    public function removeDocument($documentId)
    {
        $document = OwnerDocument::find($documentId);

        $document->delete();

        $this->documents = array_filter($this->documents, function($doc) use ($documentId) {
            return $doc['id'] !== $documentId;
        });

        $this->documents = array_values($this->documents);
        $this->alert('success', __('messages.documentDeleted'));
        
    }
    public function render()
    {
        return view('livewire.owner.owner-documents');
    }
}
