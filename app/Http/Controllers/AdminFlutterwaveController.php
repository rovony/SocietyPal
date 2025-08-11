<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Society;
use Illuminate\Http\Request;
use App\Models\MaintenanceApartment;
use App\Models\AdminFlutterwavePayment;

class AdminFlutterwaveController extends Controller
{
    private $secretKey;

    public function setKeys($societyHash)
    {
        $society = Society::where('hash', $societyHash)->first();
        if (!$society) {
            throw new \Exception('Invalid webhook URL. Please check the society hash.');
        }

        $credential = $society->paymentGateways;
        $this->secretKey = $credential->flutterwave_secret;
    }

    public function handleGatewayWebhook(Request $request, $societyHash)
    {
      
        $this->setKeys($societyHash);
        $event = $request->event;
        $transactionId = $request->data['tx_ref'] ?? null;
        $transactionData = $request->data ?? null;

        if (!$transactionId) {
            return response()->json(['message' => 'Invalid webhook payload'], 400);
        }
        if ($event === 'charge.completed') {
            $flutterwavePayment = AdminFlutterwavePayment::where('flutterwave_payment_id', $transactionId)->first();
            if (!$flutterwavePayment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }

            $flutterwavePayment->payment_status = 'completed';
            $flutterwavePayment->save();

            $order = MaintenanceApartment::find($flutterwavePayment->maintenance_apartment_id);
            $order->cost = $flutterwavePayment->amount;
            $order->paid_status = 'paid';
            $order->save();
        }


        if ($event === 'charge.failed') {

            $flutterwavePayment = AdminFlutterwavePayment::where('flutterwave_payment_id', $transactionId)->first();
            if ($flutterwavePayment) {
                $flutterwavePayment->payment_status = 'failed';
                $flutterwavePayment->payment_error_response = json_encode($transactionData);
                $flutterwavePayment->save();


            }


            return response()->json(['message' => 'Payment failed event processed']);
        }
        return response()->json(['message' => 'Event not processed'], 400);
    }



    public function paymentFailed(Request $request)
    {
        $transactionId = $request->tx_ref;
        $errorMessage = json_encode([
            'code' => $data['error']['code'] ?? 'unknown_error',
            'message' => $data['error']['message'] ?? 'Payment failed',
        ]);

        $flutterwavePayment = AdminFlutterwavePayment::where('flutterwave_payment_id', $transactionId)->first();

        if ($flutterwavePayment) {
            $flutterwavePayment->payment_error_response = $errorMessage;
            $flutterwavePayment->payment_status = 'failed';
            $flutterwavePayment->save();
        }

        return response()->json(['message' => 'Payment failed event processed']);
    }


    public function paymentMainSuccess(Request $request){
        $status = $request->status;
        $transactionId = $request->tx_ref;

        $flutterwavePayment = AdminFlutterwavePayment::where('flutterwave_payment_id', $transactionId)->first();

        if (!$flutterwavePayment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }
        if ($status !== 'successful') {
            $flutterwavePayment->payment_status = 'failed';
            $flutterwavePayment->payment_error_response = $request->data ? json_encode($request->data) : json_encode(['message' => 'Payment failed']);
            $flutterwavePayment->save();
            session()->flash('flash.banner', 'Payment Process failed!');
            session()->flash('flash.bannerStyle', 'danger');
            return redirect()->route('maintenance.index');
        }



        $flutterwavePayment->payment_status = 'completed';
        $flutterwavePayment->payment_date = now();
        $flutterwavePayment->save();

        $order = MaintenanceApartment::find($flutterwavePayment->maintenance_apartment_id);
        $order->cost = $flutterwavePayment->amount;
        $order->payment_date = now();
        $order->paid_status = 'paid';
        $order->save();
        if($flutterwavePayment->payment_status = 'completed'){
                Payment::create([
                    'maintenance_apartment_id' => $flutterwavePayment->maintenance_apartment_id,
                    'payment_method' => 'flutterwave',
                    'amount' => $flutterwavePayment->amount,
                    'transaction_id' => $transactionId,
                ]);
                session()->flash('flash.banner',  'Payment processed successfully!');
                session()->flash('flash.bannerStyle', 'success');
                return redirect()->route('maintenance.index');
            }
        }
    }
