<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Society;
use Illuminate\Http\Request;
use App\Models\PaypalPayment;
use App\Models\MaintenanceApartment;


class PaypalWebhookController extends Controller
{
    private $clientId;
    private $clientSecret;
    private $accessToken;

    public function setKeys($societyHash)
    {
        $society = Society::where('hash', $societyHash)->first();
        if (!$society) {
            throw new \Exception('Invalid webhook URL');
        }

        $credential = $society->paymentGateways;

        $this->clientId = $credential->paypal_client_id_data;
        $this->clientSecret = $credential->paypal_secret_data;

        if (is_null($this->clientId) || is_null($this->clientSecret)) {
            throw new \Exception('PayPal credentials are not set correctly.');
        }


    }


    public function handleGatewayWebhook(Request $request, $societyHash)
    {
        info('Webhook received', [
            'request' => $request->all(),
        ]);

        $this->setKeys($societyHash);

        $event = $request->event_type;
        info('Event type', [
            'event' => $event,
        ]);

        if ($event === 'PAYMENT.CAPTURE.DENIED') {
            try {
            $resource = $request->resource;
            $orderId = $resource['supplementary_data']['related_ids']['order_id'];

            $paypalPayment = PaypalPayment::where('paypal_payment_id', $orderId)->first();

            if ($paypalPayment) {
                $paypalPayment->payment_status = 'failed';
                $paypalPayment->save();
            }

            return response()->json(['message' => 'Payment failed event processed']);
            } catch (\Exception $e) {
            return response()->json(['message' => 'Error handling payment failed event', 'error' => $e->getMessage()], 400);
            }
        }

        if ($event === 'PAYMENT.CAPTURE.COMPLETED') {
            try {
                $resource = $request->resource;
                $orderId = $resource['supplementary_data']['related_ids']['order_id'];
                info('Order ID', [
                    'order_id' => $orderId,
                ]);
                $transactionId = $resource['id'] ?? null;

                $paypalPayment = PaypalPayment::where('paypal_payment_id', $orderId)->first();

                if (!$paypalPayment) {
                    return response()->json(['message' => 'Payment not found'], 404);
                }

                $paypalPayment->payment_status = 'completed';
                $paypalPayment->payment_date = now();
                $paypalPayment->save();

                $order = MaintenanceApartment::find($paypalPayment->maintenance_apartment_id);
                $order->cost = $paypalPayment->amount;
                $order->payment_date = $paypalPayment->payment_date;
                $order->paid_status = 'paid';
                $order->save();

                $existingPayment = Payment::where('transaction_id', $transactionId)->first();
                if (!$existingPayment) {
                    Payment::create([
                        'maintenance_apartment_id' => $paypalPayment->maintenance_apartment_id,
                        'payment_method' => 'paypal',
                        'amount' => $paypalPayment->amount,
                        'transaction_id' => $transactionId,
                    ]);
                }
                info('Payment updated', ['order' => $order]);

                return response()->json(['message' => 'Capture event processed successfully']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error handling capture event', 'error' => $e->getMessage()], 400);
            }
        }

        return response()->json(['message' => 'Event not processed']);
    }





}
