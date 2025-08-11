<?php

namespace App\Livewire\Tenants;

use App\Helper\Files;
use App\Models\Tenant;
use App\Models\TenantFile;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class TenantDocument extends Component
{
    use WithFileUploads, LivewireAlert;

    public $tenant;
    public $tenantId;
    public $documentName = '';
    public $documentFile;
    public $documents = [];

    public function mount()
    {
        $this->tenantId = $this->tenantId;
        $this->tenant = Tenant::with('user', 'documents', 'apartments')->findOrFail($this->tenantId);
        $this->documents = $this->tenant->documents()->get(['id', 'filename', 'hashname'])->toArray();
    }

    public function addDocument()
    {
        $this->validate(
            [
                'documentName' => 'required|string|max:255',
                'documentFile' => 'required|file|max:1024',
            ],
            [
                'documentName.required' => __('messages.addDocumentRequired'),
                'documentFile.required' => __('messages.uploadDocumentRequired'),
            ]
        );

        $documentPath = Files::uploadLocalOrS3($this->documentFile, '/');

        $document = TenantFile::create([
            'tenant_id' => $this->tenantId,
            'filename' => $this->documentName,
            'hashname' => $documentPath,
        ]);

        $this->documents[] = [
            'id' => $document->id,
            'filename' => $this->documentName,
            'hashname' => $documentPath,
        ];

        $this->documentName = '';
        $this->documentFile = null;

        $this->alert('success', __('messages.documentUploaded'));
    }

    public function removeDocument($documentId)
    {
        $document = TenantFile::find($documentId);

        $document->delete();

        $this->documents = array_filter($this->documents, function ($doc) use ($documentId) {
            return $doc['id'] !== $documentId;
        });

        $this->documents = array_values($this->documents);

        $this->alert('success', __('messages.documentDeleted'));
    }
    public function render()
    {
        return view('livewire.tenants.tenant-document');
    }
}
