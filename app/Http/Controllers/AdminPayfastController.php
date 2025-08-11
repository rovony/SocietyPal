<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\{
    Payment,
    Society,
    MaintenanceApartment,
    AdminPayfastPayment
};

class AdminPayfastController extends Controller
{
    public function paymentMainSuccess(Request $request)
    {
        $reference = $request->reference;

        if (!$reference) {
            return $this->flashAndRedirect('No reference supplied!', 'danger');
        }

        $payfastPayment = AdminPayfastPayment::where('payfast_payment_id', $reference)->first();

        if (!$payfastPayment) {
            return $this->flashAndRedirect('Payment record not found!', 'danger');
        }

        switch ($payfastPayment->payment_status) {
            case 'completed':
                $this->markPaymentAsPaid($payfastPayment);
                return $this->flashAndRedirect('Payment processed successfully!', 'success');

            case 'pending':
                return $this->flashAndRedirect('Payment is still pending confirmation from PayFast.', 'info');

            default:
                return $this->flashAndRedirect('Payment failed or was cancelled.', 'danger');
        }
    }

    public function paymentFailed(Request $request)
    {
        $reference = $request->input('reference') ?? $request->input('m_payment_id');

        if ($reference) {
            $payment = AdminPayfastPayment::where('payfast_payment_id', $reference)->first();
            if ($payment) {
                $payment->update([
                    'payment_status' => 'failed',
                    'payment_error_response' => json_encode([
                        'message' => 'User cancelled or PayFast failed to process the payment.'
                    ])
                ]);
            }
        }

        return $this->flashAndRedirect('PayFast payment was cancelled or failed.', 'danger');
    }

    public function payfastNotify(Request $request, $company, $reference)
    {
        $data = $request->except('signature');
        $society = Society::where('hash', $company)->first();

        if (!$society) {
            return response('Invalid society', 404);
        }

        $status = $data['payment_status'] ?? 'failed';
        $amountGross = $data['amount_gross'] ?? 0;
        $payfastId = $data['pf_payment_id'] ?? null;

        if (strtoupper($status) === 'COMPLETE') {
            $payment = AdminPayfastPayment::where('payfast_payment_id', $reference)->first();

            if ($payment) {
                $payment->update([
                    'payment_status' => 'completed',
                    'payment_error_response' => json_encode(['message' => 'Payment completed successfully.']),
                ]);


                Payment::create([
                    'maintenance_apartment_id' => $payment->maintenance_apartment_id,
                    'payment_method' => 'payfast',
                    'amount' => $amountGross,
                    'transaction_id' => $payfastId,
                ]);

                $this->markPaymentAsPaid($payment, $amountGross);

            }
        }

        return response('OK', 200);
    }

    /**
     * Flash message and redirect to maintenance index
     */
    private function flashAndRedirect(string $message, string $style)
    {
        session()->flash('flash.banner', $message);
        session()->flash('flash.bannerStyle', $style);
        return redirect()->route('maintenance.index');
    }

    /**
     * Update apartment as paid
     */
    private function markPaymentAsPaid(AdminPayfastPayment $payfastPayment, $amount = null)
    {
        $apartment = MaintenanceApartment::find($payfastPayment->maintenance_apartment_id);

        if ($apartment) {
            $apartment->update([
                'cost' => $amount ?? $payfastPayment->amount,
                'payment_date' => now(),
                'paid_status' => 'paid',
            ]);
        }
    }
}
