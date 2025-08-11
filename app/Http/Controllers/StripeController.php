<?php

namespace App\Http\Controllers;

use Error;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Society;
use Illuminate\Http\Request;
use App\Models\GlobalInvoice;
use App\Models\SocietyPayment;
use App\Models\GlobalSubscription;
use App\Models\Package;
use App\Models\SuperadminPaymentGateway;
use App\Notifications\SocietyUpdatedPlan;
use App\Notifications\SendOrderBill;
use Illuminate\Support\Facades\Notification;

class StripeController extends Controller
{
    public function licensePayment(Request $request)
    {
        $paymentGateway = SuperadminPaymentGateway::first();
        $societyPayment = SocietyPayment::findOrFail($request->license_payment);
        $society = Society::find($societyPayment->society_id);
        $stripe = new \Stripe\StripeClient($paymentGateway->stripe_secret);
        if (!$society->stripe_id) {
            $customer = $stripe->customers->create([
                'name' => $society->name,
                'email' => $society->email,
            ]);
            $society->stripe_id = $customer->id;
            $society->save();
        }

        $package = Package::find($request->input('package_id'));

        if ($package->package_type->value === 'standard') {
            $planType = $request->input('package_type');
            $priceId = $planType === 'annual' ? $package->stripe_annual_plan_id : $package->stripe_monthly_plan_id;

            // Check if the price ID exists in Stripe
            if (!isset($priceId) || trim($priceId) === '') {
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.invalidStripePlan'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }

            try {
                $stripe->prices->retrieve($priceId);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                return redirect()->route('dashboard')->with([
                    'flash.banner' => $e->getMessage(),
                    'flash.bannerStyle' => 'danger'
                ]);
            }

            try {
            $session = $stripe->checkout->sessions->create([
                'customer' => $society->stripe_id,
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('stripe.license_success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('dashboard'),
                'client_reference_id' => $societyPayment->id,
            ]);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                if (strpos($e->getMessage(), 'You cannot combine currencies on a single customer') !== false) {
                    return redirect()->route('dashboard')->with([
                        'flash.banner' => __('messages.currencyMismatch'),
                        'flash.bannerStyle' => 'danger'
                    ]);
                } else {
                    return redirect()->route('dashboard')->with([
                        'flash.banner' => $e->getMessage(),
                        'flash.bannerStyle' => 'danger'
                    ]);
                }
            }

            $societyPayment->stripe_session_id = $session->id;
            $societyPayment->save();
            header('HTTP/1.1 303 See Other');
            return redirect($session->url);
        } else {
            // Lifetime package or other
            $checkoutSession = $stripe->checkout->sessions->create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($currency?->currency_code ?? 'usd'),
                        'product_data' => [
                            'name' => 'License Payment for ' . global_setting()->name,
                        ],
                        'unit_amount' => floatval($societyPayment->amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.license_success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('dashboard'),
                'client_reference_id' => $societyPayment->id
            ]);

            $societyPayment->stripe_session_id = $checkoutSession->id;
            $societyPayment->package_type = 'lifetime';
            $societyPayment->save();
            header('HTTP/1.1 303 See Other');
            return redirect($checkoutSession->url);
        }
    }

    public function licenseSuccess()
    {
        $paymentGateway = SuperadminPaymentGateway::first();
        $payment = SocietyPayment::where('stripe_session_id', request()->session_id)->firstOrFail();
        $stripe = new \Stripe\StripeClient($paymentGateway->stripe_secret);

        try {
            $session = $stripe->checkout->sessions->retrieve(request()->session_id);

            $planId = null;
            $subscriptionId = null;
            // Retrieve the invoice and payment intent
            if ($session->invoice) {
                $invoice = $stripe->invoices->retrieve($session->invoice);
                $paymentIntent = $invoice->payment_intent;

                $lineItem = $invoice->lines->data[0];
                $planId = $lineItem->plan->id;
                $subscriptionId = $lineItem->subscription;
            } else {
                $paymentIntent = $session->payment_intent;
            }

            $payment->stripe_payment_intent = $paymentIntent;
            $payment->status = 'paid';
            $payment->payment_date_time = now()->toDateTimeString();
            $payment->save();
            $society = Society::find($payment->society_id);
            $society->package_id = $payment->package_id;
            $society->package_type = $payment->package_type;
            $society->trial_ends_at = null;
            $society->is_active = true;
            $society->status = 'active';
            $society->license_expire_on = null;
            $society->save();

            GlobalSubscription::where('society_id', $society->id)
                ->where('subscription_status', 'active')
                ->update(['subscription_status' => 'inactive']);

            // Create new Subscription entry
            $subscription = new GlobalSubscription();
            $subscription->transaction_id = $paymentIntent;
            $subscription->society_id = $society->id;
            $subscription->package_type = $society->package_type;
            $subscription->currency_id = $payment->currency_id;
            $subscription->stripe_id = $society->stripe_id;
            $subscription->quantity = 1;
            $subscription->package_id = $society->package_id;
            $subscription->gateway_name = 'stripe';
            $subscription->subscription_status = 'active';
            $subscription->subscription_id = $subscriptionId;
            $subscription->ends_at = $society->license_expire_on ?? null;
            $subscription->subscribed_on_date = now()->format('Y-m-d H:i:s');
            $subscription->save();


            // Check if the invoice already exists and update or create accordingly
            if ($subscription) {
                $invoice = GlobalInvoice::updateOrCreate(
                    ['transaction_id' => $subscription->transaction_id],
                    [
                        'society_id' => $society->id,
                        'currency_id' => $subscription->currency_id,
                        'package_id' => $subscription->package_id,
                        'global_subscription_id' => $subscription->id,
                        'package_type' => $subscription->package_type,
                        'plan_id' => $planId,
                        'total' => $payment->amount,
                        'gateway_name' => 'stripe',
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
            request()->session()->flash('flash.banner', __('messages.planUpgraded'));
            request()->session()->flash('flash.bannerStyle', 'success');
            request()->session()->flash('flash.link', route('settings.index', ['tab' => 'billing']));

            return redirect()->route('dashboard')->with('livewire', true);
        } catch (\Exception $e) {
            logger(['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.paymentError'),
                'flash.bannerStyle' => 'danger'
            ]);
        }
    }

}