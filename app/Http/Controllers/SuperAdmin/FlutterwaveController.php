<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalSubscription;
use App\Models\Package;
use App\Models\SuperadminPaymentGateway;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Notifications\SocietyUpdatedPlan;
use Illuminate\Support\Facades\Notification;
use App\Models\SocietyPayment;
use App\Models\Society;
use App\Models\GlobalInvoice;
use Carbon\Carbon;

class FlutterwaveController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $paymentGateway = SuperadminPaymentGateway::first();
        $societyPayment = SocietyPayment::findOrFail($request->payment_id);
        $society = society()::find($societyPayment->society_id);
        $package = Package::find($request->input('package_id'));

        if (!$package) {
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.packageNotFound'),
                'flash.bannerStyle' => 'danger'
            ]);
        }
        $currencyCode = $society->currency->currency_code;
        if ($package->package_type->value === 'standard') {
            $planType = $request->input('package_type');
            $planId = $planType === 'annual' ? $package->flutterwave_annual_plan_id : $package->flutterwave_monthly_plan_id;

            // Check if the plan ID exists
            if (!isset($planId) || trim($planId) === '') {
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.invalidFlutterwavePlan'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }

            // Verify plan ID exists in Flutterwave
            $planCheckResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paymentGateway->flutterwave_secret,
                'Content-Type' => 'application/json',
            ])->get("https://api.flutterwave.com/v3/payment-plans/{$planId}");

            if ($planCheckResponse->failed() || !isset($planCheckResponse->json()['data'])) {
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.flutterwavePlanNotFound'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }

            $transactionRef = "FLW_" . uniqid();
            $redirectUrl = route('flutterwave.callback');
            $planDetails = $planCheckResponse->json()['data'];
            $currencyCode = $planDetails['currency'];
            $amount = $planDetails['amount'];

            $payload = [
                'tx_ref' => $transactionRef,
                'amount' => $amount,
                'currency' => $currencyCode,
                'payment_options' => 'card, banktransfer',
                'redirect_url' => $redirectUrl,
                'customer' => [
                    'email' => $society->email,
                    'name' => $society->name,
                ],
                'customizations' => [
                    'title' => 'License Payment',
                    'description' => 'Payment for Society License',
                ],
                'payment_plan' => $planId // Attach the plan ID
            ];
        } else {
            // Lifetime package or other one-time payment
            $transactionRef = "FLW_" . uniqid();
            $redirectUrl = route('flutterwave.callback');

            $payload = [
                'tx_ref' => $transactionRef,
                'amount' => $societyPayment->amount,
                'currency' => $currencyCode,
                'payment_options' => 'card, banktransfer',
                'redirect_url' => $redirectUrl,
                'customer' => [
                    'email' => $society->email,
                    'name' => $society->name,
                ],
                'customizations' => [
                    'title' => 'License Payment',
                    'description' => 'Payment for Society License',
                ]
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $paymentGateway->flutterwave_secret,
            'Content-Type' => 'application/json',
        ])->post('https://api.flutterwave.com/v3/payments', $payload);

        $responseData = $response->json();

        if (isset($responseData['data']['link'])) {
            $societyPayment->flutterwave_payment_ref = $transactionRef;
            $societyPayment->save();
            return redirect()->away($responseData['data']['link']);
        }

        return redirect()->route('dashboard')->with([
            'flash.banner' => __('messages.paymentError'),
            'flash.bannerStyle' => 'danger'
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $paymentGateway = SuperadminPaymentGateway::first();
        $transactionRef = $request->query('tx_ref');

        if (!$transactionRef) {
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.transactionReferenceMissing'),
                'flash.bannerStyle' => 'danger'
            ]);
        }

        $societyPayment = SocietyPayment::where('flutterwave_payment_ref', $transactionRef)->first();
        if (!$societyPayment) {
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.invalidTransactionReference'),
                'flash.bannerStyle' => 'danger'
            ]);
        }

        try {
            // Verify the transaction with Flutterwave API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paymentGateway->flutterwave_secret,
                'Content-Type' => 'application/json',
            ])->get("https://api.flutterwave.com/v3/transactions/{$request->query('transaction_id')}/verify");

            $responseData = $response->json();

            if (!isset($responseData['status']) || !isset($responseData['data'])) {
                $societyPayment->status = 'failed';
                $societyPayment->save();
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.paymentVerificationFailed'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }

            $paymentSuccess = ($responseData['status'] === 'success' && $responseData['data']['status'] === 'successful');

            if (!$paymentSuccess) {
                $societyPayment->status = 'failed';
                $societyPayment->save();
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.paymentVerificationFailed'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }

            $FlutterwaveTransactionId = $responseData['data']['id'] ?? null;
            $subscriptionId = null;
            if ($FlutterwaveTransactionId) {

                $sub = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $paymentGateway->flutterwave_secret,
                    'Content-Type' => 'application/json',
                ])->get("https://api.flutterwave.com/v3/subscriptions", [
                    'transaction_id' => $FlutterwaveTransactionId,
                    'status' => 'active',
                ]);

                if (isset($sub['data']) && is_array($sub['data']) && count($sub['data']) > 0) {
                    $subscriptionId = $sub['data'][0]['id'] ?? null;
                }
            }

            $transactionId = $responseData['data']['tx_ref'] ?? null;
            $amount = $responseData['data']['amount'] ?? null;
            $customerId = $responseData['data']['customer']['id'] ?? null;

            $societyPayment->flutterwave_transaction_id = $transactionId;
            $societyPayment->amount = $amount;
            $societyPayment->status = 'paid';
            $societyPayment->payment_date_time = now()->toDateTimeString();
            $societyPayment->save();

            // Fetch the society details
            $society = Society::find($societyPayment->society_id);
            $society->package_id = $societyPayment->package_id;
            $society->package_type = $societyPayment->package_type;
            $society->trial_ends_at = null;
            $society->is_active = true;
            $society->status = 'active';
            $society->license_expire_on = null;
            $society->license_updated_at = now();
            $society->subscription_updated_at = now();
            $society->save();
            // Deactivate existing subscriptions
            GlobalSubscription::where('society_id', $society->id)
                ->where('subscription_status', 'active')
                ->update(['subscription_status' => 'inactive']);

            // Create new Subscription entry
            $subscription = new GlobalSubscription();
            $subscription->transaction_id = $transactionId;
            $subscription->society_id = $society->id;
            $subscription->package_type = $society->package_type;
            $subscription->currency_id = $societyPayment->currency_id;
            $subscription->quantity = 1;
            $subscription->package_id = $society->package_id;
            $subscription->gateway_name = 'flutterwave';
            $subscription->subscription_status = 'active';
            $subscription->flutterwave_id = $FlutterwaveTransactionId;
            $subscription->flutterwave_payment_ref = $transactionRef;
            $subscription->flutterwave_status = $responseData['data']['status'] ?? null;
            $subscription->flutterwave_customer_id = $customerId;
            $subscription->subscription_id = $subscriptionId;
            $subscription->ends_at = $society->license_expire_on ?? null;
            $subscription->subscribed_on_date = now()->format('Y-m-d H:i:s');
            $subscription->save();

            $package = Package::find($societyPayment->package_id);

            if (!$package) {
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.packageNotFound'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }
            // Check if invoice exists, otherwise create/update
            if ($subscription) {
                $invoice = GlobalInvoice::updateOrCreate(
                    ['transaction_id' => $subscription->transaction_id],
                    [
                        'society_id' => $society->id,
                        'currency_id' => $subscription->currency_id,
                        'package_id' => $subscription->package_id,
                        'global_subscription_id' => $subscription->id,
                        'package_type' => $subscription->package_type,
                        'total' => $amount, // Store amount
                        'plan_id' => match ($societyPayment->package_type) {
                            'lifetime' => null,
                            'annual' => $package->flutterwave_annual_plan_id,
                            'monthly' => $package->flutterwave_monthly_plan_id,
                            default => null
                        },
                        'invoice_id' => $transactionRef,
                        'gateway_name' => 'flutterwave',
                        'status' => 'active',
                    ]
                );

                if (!$invoice->pay_date) {
                    $invoice->pay_date = now();
                }

                $nextPayDate = $subscription->package_type === 'monthly'
                    ? Carbon::parse($invoice->pay_date)->addMonth()
                    : Carbon::parse($invoice->pay_date)->addYear();

                $invoice->next_pay_date = $nextPayDate;
                $invoice->save();
            }

                // Notify superadmin
            $generatedBy = User::withoutGlobalScopes()->whereNull('society_id')->first();
            Notification::send($generatedBy, new SocietyUpdatedPlan($society, $subscription->package_id));

            // Notify society admin
            $societyAdmin = $society->societyAdmin($society);
            Notification::send($societyAdmin, new SocietyUpdatedPlan($society, $subscription->package_id));

            session()->forget('society');
            session()->flash('flash.banner', __('messages.planUpgraded'));
            session()->flash('flash.bannerStyle', 'success');
            session()->flash('flash.link', route('settings.index', ['tab' => 'billing']));

            return redirect()->route('dashboard')->with('livewire', true);

        } catch (\Exception) {
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.paymentError'),
                'flash.bannerStyle' => 'danger'
            ]);
        }
    }
}
