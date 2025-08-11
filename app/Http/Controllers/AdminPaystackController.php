<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AdminPaystackPayment;
use App\Models\MaintenanceApartment;

class AdminPaystackController extends Controller
{
    private $secretKey;

    /**
     * Set Paystack secret key based on society hash.
     */
    private function setKeys(string $societyHash): void
    {
        $society = Society::where('hash', $societyHash)->firstOrFail();
        $this->secretKey = $society->paymentGateways->paystack_secret_data;
    }

    /**
     * Handle Paystack webhook notifications.
     */
    public function handleGatewayWebhook(Request $request, string $societyHash)
    {
        $this->setKeys($societyHash);
        $payload = $request->all();
        $event = $payload['event'] ?? null;
        $reference = $payload['data']['reference'] ?? null;

        if (!$reference) {
            return response()->json(['message' => 'Invalid webhook payload'], 400);
        }

        $payment = AdminPaystackPayment::where('paystack_payment_id', $reference)->first();
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if ($event === 'charge.success') {
            $this->markPaymentAsCompleted($payment);
            return response()->json(['message' => 'Payment successful']);
        }

        if ($event === 'charge.failed') {
            $payment->update([
                'payment_status' => 'failed',
                'payment_error_response' => json_encode($payload),
            ]);
            return response()->json(['message' => 'Payment failed event processed']);
        }

        return response()->json(['message' => 'Event not handled'], 400);
    }

    /**
     * Handle redirect after failed payment.
     */
    public function paymentFailed(Request $request)
    {
        $reference = $request->reference;
        $errorMessage = json_encode([
            'code' => $request->input('error.code', 'unknown_error'),
            'message' => $request->input('error.message', 'Payment failed'),
        ]);

        $payment = AdminPaystackPayment::where('paystack_payment_id', $reference)->first();
        if ($payment) {
            $payment->update([
                'payment_status' => 'failed',
                'payment_error_response' => $errorMessage,
            ]);
        }

        session()->flash('flash.banner', 'Payment process failed!');
        session()->flash('flash.bannerStyle', 'danger');

        return redirect()->route('maintenance.index');
    }

    /**
     * Handle redirect after successful payment.
     */
    public function paymentMainSuccess(Request $request)
    {
        $reference = $request->reference;
        if (!$reference) {
            return $this->redirectWithMessage('No reference supplied!', 'danger');
        }

        $payment = AdminPaystackPayment::where('paystack_payment_id', $reference)->first();
        if (!$payment) {
            return $this->redirectWithMessage('Payment not found!', 'danger');
        }

        $secretKey = auth()->user()->society->paymentGateways->paystack_secret_data;
        $response = Http::withToken($secretKey)
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        $data = $response->json();

        if (isset($data['status'], $data['data']) && $data['status'] === true && $data['data']['status'] === 'success') {
            $this->markPaymentAsCompleted($payment, true);
            return $this->redirectWithMessage('Payment processed successfully!', 'success');
        }

        // Failed fallback
        $payment->update([
            'payment_status' => 'failed',
            'payment_error_response' => json_encode($data['data'] ?? []),
        ]);

        return $this->redirectWithMessage('Payment process failed!', 'danger');
    }

    /**
     * Mark payment and order as completed, and create a Payment record.
     */
    private function markPaymentAsCompleted(AdminPaystackPayment $payment, bool $createPayment = false): void
    {
        $payment->update([
            'payment_status' => 'completed',
            'payment_date' => now(),
        ]);

        $order = MaintenanceApartment::find($payment->maintenance_apartment_id);
        if ($order) {
            $order->update([
                'cost' => $payment->amount,
                'payment_date' => now(),
                'paid_status' => 'paid',
            ]);
        }

        if ($createPayment) {
            Payment::create([
                'maintenance_apartment_id' => $payment->maintenance_apartment_id,
                'payment_method' => 'paystack',
                'amount' => $payment->amount,
                'transaction_id' => $payment->paystack_payment_id,
            ]);
        }
    }

    /**
     * Utility for flashing message and redirecting.
     */
    private function redirectWithMessage(string $message, string $type)
    {
        session()->flash('flash.banner', $message);
        session()->flash('flash.bannerStyle', $type);
        return redirect()->route('maintenance.index');
    }
}
