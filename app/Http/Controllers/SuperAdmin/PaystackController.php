<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalInvoice;
use App\Models\GlobalSubscription;
use App\Models\Package;
use App\Models\User;
use App\Traits\SuperAdmin\PaystackSettings;
use Unicodeveloper\Paystack\Paystack;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SocietyUpdatedPlan;
use App\Models\SocietyPayment;

class PaystackController extends Controller
{
    use PaystackSettings;

    protected $client;

    /**
     * Redirect the User to Paystack Payment Page
     */
    public function initiatePaystackPayment(Request $request)
    {
        $this->setPaystackConfigs();
        $package = Package::find($request->package_id);
        $paystack = new Paystack();
        $amount = 0;
        if ($package->package_type === 'monthly') {
            $amount = $package->monthly_price; 
        } elseif ($package->package_type === 'annual') {
            $amount = $package->annual_price; 
        } else {
            $amount = $package->price *100; 
        }

        $request->first_name = $request->name;
        $request->orderID = '1';
        $request->amount = $amount;
        $request->quantity = '1';
        $request->callback_url = route('paystack.callback');
        $request->reference = $paystack->genTranxRef();
        $request->key = config('paystack.secretKey');
        $request->plan = $package->{'paystack_' . $request->package_type . '_plan_id'};

        $subscription = new GlobalSubscription();

        $subscription->society_id = society()->id;
        $subscription->package_id = $package->id;
        $subscription->currency_id = $package->currency_id;
        $subscription->package_type = $request->package_type;
        $subscription->quantity = 1;
        $subscription->gateway_name = 'paystack';
        $subscription->subscription_status = 'inactive';
        $subscription->subscribed_on_date = now()->format('Y-m-d H:i:s');
        $subscription->save();

        $request->metadata = [
            'subscription_id' => $subscription->id,
            'package_amount' => $amount,
            'payment_id' => $request->payment_id,
        ];

        // Customer details
        $request->email = society()->email;
        $request->phone = society()->phone_number;
        $request->fname = society()->name;
        $request->additional_info = [
            'society_id' => society()->id,
            'subscription_id' => $subscription->id,
        ];

        $customer = $paystack->createCustomer();

        $subscription->customer_id = $customer['data']['customer_code'];
        $subscription->save();

        session([
            'subscription_id' => $subscription->id,
            'package_amount' => $amount,
            'payment_id' => $request->payment_id,
        ]);

        return $paystack->getAuthorizationUrl($request)->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $this->setPaystackConfigs();
        $paystack = new Paystack();
        $paymentDetails = $paystack->getPaymentData();
        $subscriptionCustomer = $paystack->getCustomerSubscriptions($paymentDetails['data']['customer']['id']);

        $expectedSubscriptionId = $paymentDetails['data']['customer']['metadata']['subscription_id'] ?? null;
        $expectedPlanId = $paymentDetails['data']['plan'] ?? null;
        $subscriptionCode = null;
    
        foreach ($subscriptionCustomer as $subscription) {
            $customerMetadata = json_decode($subscription['customer']['metadata'] ?? '{}', true);
            $planId = $subscription['plan']['plan_code'] ?? null;
            
            if (!empty($customerMetadata['subscription_id']) && $customerMetadata['subscription_id'] == $expectedSubscriptionId && $planId == $expectedPlanId) {
                $subscriptionCode = $subscription;
                break;
            }
            else {
                $subscriptionCode = null;
            }
        }
        $paymentId = $paymentDetails['data']['metadata']['payment_id'] ?? null; 
        if ($paymentDetails['status'] && ($paymentDetails['data']['status'] == 'success')) {

            $globalSubscription = GlobalSubscription::find($paymentDetails['data']['metadata']['subscription_id']);
            GlobalSubscription::where('society_id', $globalSubscription->society_id)->where('subscription_status', 'active')->update(['subscription_status' => 'inactive', 'ends_at' => now()]);
                $globalSubscription->subscription_status = 'active';
                $globalSubscription->subscribed_on_date = now();
                $globalSubscription->customer_id = $paymentDetails['data']['customer']['customer_code'] ?? $globalSubscription->customer_id;
                $globalSubscription->transaction_id = $paymentDetails['data']['reference'] ?? null;
                $globalSubscription->subscription_id = $subscriptionCode['subscription_code'] ?? null;
                $globalSubscription->token = $subscriptionCode['email_token'] ?? null;
                $globalSubscription->save();

            $invoice = new GlobalInvoice();
            $invoice->society_id = $globalSubscription->society_id;
            $invoice->package_id = $globalSubscription->package_id;
            $invoice->currency_id = $globalSubscription->currency_id;
            $invoice->global_subscription_id = $globalSubscription->id;
            $invoice->pay_date = now()->format('Y-m-d');
            $invoice->next_pay_date = now()->{(($globalSubscription->package_type == 'monthly') ? 'addMonth' : 'addYear')}()->format('Y-m-d');
            $invoice->status = 'active';
            $invoice->package_type = $globalSubscription->package_type;
            $invoice->gateway_name = 'paystack';
            $invoice->total = $paymentDetails['data']['amount'] / 100;
            $invoice->amount = $paymentDetails['data']['amount'] / 100;
            $invoice->transaction_id = $paymentDetails['data']['reference'];
            $invoice->token = $paymentDetails['data']['authorization']['authorization_code'];
            $invoice->signature = $paymentDetails['data']['authorization']['signature'];
            $invoice->subscription_id = $subscriptionCode['subscription_code'] ?? null;
            $invoice->save();

            if ($paymentId) {
                $societyPayment = SocietyPayment::find($paymentId);
    
                if ($societyPayment) {
                    $societyPayment->amount = $paymentDetails['data']['amount'] / 100; // Paystack amount divided by 100
                    $societyPayment->status = 'paid';
                    $societyPayment->payment_date_time = now()->toDateTimeString();
                    $societyPayment->transaction_id = $paymentDetails['data']['reference'];
                    $societyPayment->save();
                }
            }

            $society = society();
            $society->package_id = $globalSubscription->package_id;
            $society->package_type = $globalSubscription->package_type;
            $society->status = 'active';
            $society->license_expire_on = null;
            $society->save();

            $generatedBy = User::withoutGlobalScopes()->whereNull('society_id')->first();
            Notification::send($generatedBy, new SocietyUpdatedPlan($society, $globalSubscription->package_id));

            // Notify society admin
            $societyAdmin = $society->societyAdmin($society);
            Notification::send($societyAdmin, new SocietyUpdatedPlan($society, $globalSubscription->package_id));

            session()->forget('society');
            request()->session()->flash('flash.banner', __('messages.planUpgraded'));
            request()->session()->flash('flash.bannerStyle', 'success');
            request()->session()->flash('flash.link', route('settings.index', ['tab' => 'billing']));

            return redirect()->route('dashboard')->with('livewire', true);
        }
        else {
            if ($paymentId) {
                $societyPayment = SocietyPayment::find($paymentId);
    
                if ($societyPayment) {
                    $societyPayment->status = 'failed';
                    $societyPayment->save();
                }
            }
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.paymentError'),
                'flash.bannerStyle' => 'danger'
            ]);
        }

    }
}
