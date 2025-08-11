<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\PaypalPayment;
use Illuminate\Support\Facades\Log;
use App\Models\MaintenanceApartment;
use Illuminate\Support\Facades\Http;
use App\Models\PaymentGatewayCredential;

class AdminPaypalController extends Controller
{


   public function success(Request $request)
    {
        info('Success request', [
            'request' => $request->all(),
        ]);
        $token = $request->query('token'); // PayPal Order ID

        if (!$token) {
            return redirect()->route('home')->withErrors(['error' => 'Missing PayPal token.']);
        }
        $paymentGateway = PaymentGatewayCredential::first();
        $clientId = $paymentGateway->paypal_client_id_data;
        $secret = $paymentGateway->paypal_secret_data;
        $captureResponse = Http::withBasicAuth($clientId, $secret)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->send('POST', "https://api-m.sandbox.paypal.com/v2/checkout/orders/{$token}/capture");


        if ($captureResponse->successful()) {
            $paypalData = $captureResponse->json();

            $amountInfo = $paypalData['purchase_units'][0]['payments']['captures'][0]['amount'] ?? [];
            $amount = $amountInfo['value'] ?? 0;
            $transactionId = $paypalData['purchase_units'][0]['payments']['captures'][0]['id'] ?? '';
            $paypalPayment = PaypalPayment::where('paypal_payment_id', $token)->first();

            if ($paypalPayment) {
                $paypalPayment->payment_status = 'completed';
                $paypalPayment->save();
            }

            $payment = new Payment();
            $payment->maintenance_apartment_id = $paypalPayment->maintenance_apartment_id;
            $payment->payment_method = 'paypal';
            $payment->amount = $amount;
            $payment->transaction_id = $transactionId;
            $payment->save();

            $maintenanceApartment = MaintenanceApartment::find($paypalPayment->maintenance_apartment_id);
            $maintenanceApartment->cost = $amount;
            $maintenanceApartment->payment_date = now();
            $maintenanceApartment->paid_status = 'paid';
            $maintenanceApartment->save();

            session()->flash('flash.banner',  'Payment processed successfully!');
            session()->flash('flash.bannerStyle', 'success');
            return redirect()->route('maintenance.index');
        }

        Log::error('PayPal capture error', [
            'response' => $captureResponse->json(),
            'status' => $captureResponse->status(),
        ]);

        return redirect()->route('home')->withErrors(['error' => 'Payment failed or could not be captured.']);
    }
    public function cancel(Request $request)
    {
        $token = $request->query('token'); // PayPal Order ID

        $paypalPayment = PaypalPayment::where('paypal_payment_id', $token)->first();

        if ($paypalPayment) {
            $paypalPayment->payment_status = 'failed';
            $paypalPayment->save();
        }

        session()->flash('flash.banner',  'Payment was cancelled.');
        session()->flash('flash.bannerStyle', 'warning');



        return redirect()->route('maintenance.index')->withErrors(['error' => 'Payment was cancelled.']);
    }


}
