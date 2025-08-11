<?php

namespace App\Http\Controllers\SuperAdmin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Package;
use App\Models\Society;
use Illuminate\Http\Request;
use App\Models\GlobalInvoice;
use App\Models\GlobalSubscription;
use Illuminate\Routing\Controller;
use App\Models\SuperadminPaymentGateway;
use App\Models\SocietyPayment;
use App\Notifications\SocietyUpdatedPlan;
use Illuminate\Support\Facades\Notification;

class FlutterwaveWebhookController extends Controller
{
    public function handleWebhook(Request $request, $hash)
    {
        // Retrieve Flutterwave settings
        $settings = SuperadminPaymentGateway::first();
        if (!$settings || $hash !== global_setting()->hash) {
            return response()->json(['error' => true, 'message' => 'Unauthorized'], 403);
        }

        $signature = $request->header('verif-hash');

        // Verify Flutterwave signature
        if (!$signature) {
            return response()->json(['error' => true, 'message' => 'Invalid signature'], 403);
        }

        $payload = $request->all();
    
        // Extract event and data
        $event = $payload['event'] ?? null;
        $data = $payload['data'] ?? [];

        switch ($event) {
            case 'charge.completed':
                return $this->handlePaymentSuccess($data);

            case 'subscription.create':
            case 'subscription.cancel':
                return $this->handleSubscriptionEvent($data);

            default:
                return response()->json(['status' => 'success']);
        }
    }

    private function handlePaymentSuccess($data)
    {
        $transactionRef = $data['tx_ref'] ?? null;
        $transactionId = $data['tx_ref'] ?? null;
        $status = $data['status'] ?? 'failed';
        $amount = $data['amount'] ?? 0;
        $currency = $data['currency'] ?? null;
        $customerEmail = $data['customer']['email'] ?? null;

        if (!$transactionRef || $status !== 'successful') {
            return response()->json(['status' => 'error', 'message' => 'Payment failed'], 400);
        }

        // Find the society associated with this payment
        $societyPayment = SocietyPayment::where('flutterwave_payment_ref', $transactionRef)->first();
        if (!$societyPayment) {
            return response()->json(['status' => 'error', 'message' => 'Payment record not found'], 404);
        }

        $society = Society::find($societyPayment->society_id);
        if (!$society) {
            return response()->json(['status' => 'error', 'message' => 'Society not found'], 404);
        }

        $package = Package::find($societyPayment->package_id);
        if (!$package) {
            return response()->json(['status' => 'error', 'message' => 'Package not found'], 404);
        }

        $societyPayment->flutterwave_transaction_id = $transactionId;
        $societyPayment->status = 'paid';
        $societyPayment->payment_date_time = now()->toDateTimeString();
        $societyPayment->save();

        $globalSubscription = GlobalSubscription::where('gateway_name', 'flutterwave')
        ->where('society_id', $society->id)
        ->latest()
        ->first();

        $existingInvoice = GlobalInvoice::where('transaction_id', $transactionId)->first();
        
        if (!$existingInvoice) {
            $invoice = new GlobalInvoice();
            $invoice->global_subscription_id = $globalSubscription->id;
            $invoice->society_id = $society->id;
            $invoice->invoice_id = $transactionRef;
            $invoice->transaction_id = $transactionId;
            $invoice->amount = $amount;
            $invoice->total = $amount;
            $invoice->currency_id = $globalSubscription->currency_id;
            $invoice->package_type = $globalSubscription->package_type;
            $invoice->package_id = $package->id;
            $invoice->pay_date = now();
            $invoice->gateway_name = 'flutterwave';
            $invoice->status = 'active';
            $invoice->plan_id = match ($globalSubscription->package_type) {
                'lifetime' => null,
                'annual' => $package->flutterwave_annual_plan_id,
                'monthly' => $package->flutterwave_monthly_plan_id,
                default => null
            };
            $invoice->save();
        }

        $society->package_id = $package->id;
        $society->status = 'active';
        $society->is_active = true;
        $society->license_expire_on = null;
        $society->save();

        $adminUsers = User::whereNull('society_id')->get();
        Notification::send($adminUsers, new SocietyUpdatedPlan($society, $package->id));

        return response()->json(['status' => 'success', 'message' => 'Payment processed successfully']);
    }

    private function handleSubscriptionEvent($data)
    {
        $subscriptionId = $data['id'] ?? null;
        $planId = $data['plan'] ?? null;
        $customerId = $data['customer']['id'] ?? null;
        $status = $data['status'] ?? 'inactive';

        if (!$subscriptionId) {
            return response()->json(['status' => 'error', 'message' => 'Missing subscription ID'], 400);
        }

        $society = Society::where('flutterwave_customer_id', $customerId)->first();
        if (!$society) {
            return response()->json(['status' => 'error', 'message' => 'Society not found'], 404);
        }

        $subscription = GlobalSubscription::where('subscription_id', $subscriptionId)->first();
        if (!$subscription) {
            $subscription = new GlobalSubscription();
            $subscription->subscription_id = $subscriptionId;
            $subscription->society_id = $society->id;
            $subscription->flutterwave_customer_id = $customerId;
            $subscription->gateway_name = 'flutterwave';
        }

        $subscription->plan_id = $planId;
        $subscription->subscription_status = $status;
        $subscription->save();

        return response()->json(['status' => 'success', 'message' => 'Subscription updated']);
    }
}
