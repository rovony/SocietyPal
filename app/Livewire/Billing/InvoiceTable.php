<?php

namespace App\Livewire\Billing;

use App\Models\GlobalInvoice;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceTable extends Component
{
    use WithPagination;
    public $search;
    public $societyId;

    public function downloadReceipt($id)
    {
        $invoice = GlobalInvoice::findOrFail($id);

        if (!$invoice) {

            $this->alert('error', __('messages.noInvoiceFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            return;
        }


        $pdf = Pdf::loadView('billing.billing-receipt', ['invoice' => $invoice]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'billing-receipt-' . uniqid() . '.pdf');
    }

    public function render()
    {
        $query = GlobalInvoice::query()
            ->with(['society', 'package'])
            ->orderByDesc('id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('society', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('package', function ($q) {
                    $q->where('package_type', 'like', '%' . $this->search . '%');
                })
                ->orWhere('gateway_name', 'like', '%' . $this->search . '%')
                ->orWhere('total', 'like', '%' . $this->search . '%')
                ->orWhere('transaction_id', 'like', '%' . $this->search . '%')
                ->orWhere('package_type', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->societyId) {
            $query->where('society_id', $this->societyId);
        }

        $invoices = $query->paginate(10);

        return view('livewire.billing.invoice-table', [
            'invoices' => $invoices
        ]);
    }
}
