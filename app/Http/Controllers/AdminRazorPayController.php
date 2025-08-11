<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Society;
use App\Models\Payment;
use App\Models\RazorpayPayment;
use App\Models\MaintenanceApartment;
use Illuminate\Support\Facades\Http;

class AdminRazorPayController extends Controller
{
    private $apiKey;
    private $secretKey;
    private $webhookSecret;

    public function setKeys($societyHash)
    {
        $society = Society::where('hash', $societyHash)->first();
        if (!$society) {
            throw new \Exception('Please enter the correct webhook url. You have entered wrong webhook url');
        }

        $credential = $society->paymentGateways;
        $this->apiKey = $credential->razorpay_key;

        $this->secretKey = $credential->razorpay_secret;
    }

    public function handleGatewayWebhook(Request $request, $societyHash)
    {
        $this->setKeys($societyHash);

        if ($request->event === 'payment.failed') {
            $this->paymentFailed($request->all());
            return response()->json(['message' => 'Payment failed event processed']);
        }

        if ($request->event !== 'payment.authorized') {
            return response()->json(['message' => 'Event not processed']);
        }

        try {
            $api = new Api($this->apiKey, $this->secretKey);

            $payment = $api->payment->fetch($request->payload['payment']['entity']['id']);

            if ($payment->status != 'authorized') {
                return response()->json(['message' => 'Payment not authorized']);
            }

            $payment->capture([
                'amount' => $payment->amount,
                'currency' => $payment->currency
            ]);

            $orderId = $payment->order_id;
            $razorpayPayment = RazorpayPayment::where('razorpay_order_id', $orderId)->first();
            if (!$razorpayPayment) {
                return response()->json(['message' => 'Payment not found'], 404);
            } else {
                $razorpayPayment->payment_status = 'completed';
                $razorpayPayment->payment_date = now();
                $razorpayPayment->save();

                $order = MaintenanceApartment::find($razorpayPayment->maintenance_apartment_id);
                $order->cost = $razorpayPayment->amount;
                $order->payment_date = $razorpayPayment->payment_date;
                $order->paid_status = 'paid';
                $order->save();

                Payment::create([
                    'maintenance_apartment_id' => $razorpayPayment->maintenance_apartment_id,
                    'payment_method' => 'razorpay',
                    'amount' => $razorpayPayment->amount,
                    'transaction_id' => $razorpayPayment->razorpay_payment_id,
                ]);

            }
            return response()->json(['message' => 'Webhook Handled Successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error handling webhook', 'error' => $e->getMessage()], 400);
        }
    }


    public function paymentFailed($payload)
    {
        $paymentId = $payload['payload']['payment']['entity']['id'];
        $code = $payload['payload']['payment']['entity']['error_code'] ?? 'unknown_error';
        $message = $payload['payload']['payment']['entity']['error_description'] ?? 'Payment failed';

        $errorMessage = json_encode(['code' => $code, 'message' => $message]);

        $orderId = $payload['payload']['payment']['entity']['order_id'];

        $razorpayPayment = RazorpayPayment::where('razorpay_order_id', $orderId)->first();

        $razorpayPayment->payment_error_response = $errorMessage;
        $razorpayPayment->payment_status = 'failed';
        $razorpayPayment->save();
    }
}






