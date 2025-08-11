<?php

namespace App\Traits;

use App\Models\Payment;

trait MakePaymentTrait
{

    /**
     * makePayment to generate payment of invoice.
     *
     * @param string|null $gateway
     * @param int|float $amount
     * @param Invoice|Collection $invoice
     * @param array|int|string $transactionId This can be single transaction id or array of transaction ids
     * @param string $status (default: 'pending')
     * @return Payment $payment
     */
    public function makePayment($maintenanceApartmentId, $paymentMethod, $amount, $transactionId = null, $balance = 0)
    {
        $payment = new Payment();
        $payment->maintenance_apartment_id = $maintenanceApartmentId;
        $payment->payment_method = $paymentMethod;
        $payment->amount = $amount;
        $payment->balance = $balance;
        $payment->transaction_id = $transactionId;
        $payment->save();

        return $payment;
    }

    public function getWebhook()
    {
        return response()->json(['message' => 'This URL should not be accessed directly. Only POST requests are allowed.']);
    }

}
