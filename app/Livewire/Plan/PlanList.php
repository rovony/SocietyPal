<?php

namespace App\Livewire\Plan;

use App\Models\User;
use App\Helper\Files;
use App\Helper\Reply;
use Razorpay\Api\Api;
use App\Models\Module;
use App\Models\Country;
use App\Models\Package;
use Livewire\Component;
use App\Enums\PackageType;
use App\Models\Society;
use Livewire\Attributes\On;
use App\Models\GlobalInvoice;
use Livewire\WithFileUploads;
use App\Models\GlobalCurrency;
use Illuminate\Support\Carbon;
use App\Scopes\SocietyScope;
use App\Models\OfflinePlanChange;
use App\Models\SocietyPayment;
use App\Models\GlobalSubscription;
use App\Models\OfflinePaymentMethod;
use App\Models\SuperadminPaymentGateway;
use App\Notifications\SocietyUpdatedPlan;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\SocietyPlanModificationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PlanList extends Component
{
    use LivewireAlert , WithFileUploads;

    public $packages;
    public $modules;
    public $currencies;
    public $selectedCurrency;
    public bool $isAnnual = false;
    public $showPaymentMethodModal = false;
    public $paymentMethods;
    public $free = false;
    public $selectedPlan;
    public $society;
    public $stripeSettings;
    public $logo;
    public $countries;
    public $methods;
    public $payFastHtml;
    public $paymentGatewayActive;
    public $offlinePaymentGateways;
    public $paymentActive;
    public $pageTitle;
    public $planId;
    public $showOnline;
    public $show = 'payment-method';
    public $offlineMethodId;
    public $offlineUploadFile;
    public $offlineDescription;
    public $selectedCurrencyCode;

    public function mount()
    {

        $this->society = Society::where('id', society()->id)->first();
        $this->paymentGatewayActive = false;
        $this->stripeSettings = SuperadminPaymentGateway::first();
        $this->currencies = GlobalCurrency::select('id', 'currency_name', 'currency_symbol')
        ->where('status', 'enable')
        ->get();

        if (in_array(true, [
            $this->stripeSettings->paypal_status,
            $this->stripeSettings->stripe_status,
            $this->stripeSettings->razorpay_status,
            $this->stripeSettings->flutterwave_status,
            $this->stripeSettings->paystack_status,
            $this->stripeSettings->mollie_status,
            $this->stripeSettings->payfast_status,
            $this->stripeSettings->authorize_status
        ])) {
            $this->paymentGatewayActive = true;
        }

        $this->methods = OfflinePaymentMethod::withoutGlobalScope(SocietyScope::class)->where('status', 'active')->whereNull('society_id')->get();
        $this->offlinePaymentGateways = $this->methods->count();

        $this->showOnline = $this->paymentGatewayActive;
        $this->paymentActive = $this->paymentGatewayActive || $this->offlinePaymentGateways > 0;

        $this->selectedCurrency = global_setting()->defaultCurrency->id ?? null;
        $this->selectedCurrencyCode = global_setting()->defaultCurrency->code ?? 'USD';
        $this->modules = Module::all();

        $this->loadAvailablePackages();

    }

    public function switchPaymentMethod($method)
    {
        $this->show = $method;
    }

    // Selected Package
    public function selectedPackage($id)
    {
        $this->resetValidation();
        $this->reset(['show', 'offlineMethodId', 'offlineUploadFile', 'offlineDescription']);

        $this->selectedPlan = Package::findOrFail($id);
        $this->stripeSettings = SuperadminPaymentGateway::first();
        $this->logo = global_setting()->logo_url;
        $this->countries = Country::all();
        $this->methods = OfflinePaymentMethod::withoutGlobalScope(SocietyScope::class)
            ->where('status', 'active')
            ->whereNull('society_id')
            ->get();
        $this->free = $this->selectedPlan->payment_type == PackageType::DEFAULT || $this->selectedPlan->is_free;

        // Switching payment method based on the selected plan and modal.
        if ($this->free || !$this->paymentActive) {
            $this->showPaymentMethodModal = true;
            return;
        }

        if ($this->paymentGatewayActive) {
            $this->offlinePaymentGateways == 0 ? $this->handleOnlinePayments() : $this->showPaymentMethodModal = true;
        } else {
            $this->showPaymentMethodModal = true;
        }
    }

    // Online Payment Handling
    private function handleOnlinePayments()
    {
        $activeGateways = collect([
            'stripe' => $this->stripeSettings->stripe_status,
            'razorpay' => $this->stripeSettings->razorpay_status,
            'flutterwave' => $this->stripeSettings->flutterwave_status,
            'paypal' => $this->stripeSettings->paypal_status,
            'paystack' => $this->stripeSettings->paystack_status,
            'payfast' => $this->stripeSettings->payfast_status,
        ])->filter();

        if ($activeGateways->count() > 1) {
            $this->showPaymentMethodModal = true;
            return;
        }

        $gateway = $activeGateways->keys()->first();

        match ($gateway) {
            'stripe' => $this->initiateStripePayment(),
            'razorpay' => $this->razorpaySubscription($this->selectedPlan->id),
            'flutterwave' => $this->initiateFlutterwavePayment(),
            'paypal' => $this->initiatePaypalPayment(),
            'paystack' => $this->initiatePaystackPayment(),
            'payfast' => $this->initiatePayfastPayment(),
            default => $this->showPaymentMethodModal = true,
        };
    }

    // Offline Payment Submit
    public function offlinePaymentSubmit()
    {
        $this->validate([
            'offlineMethodId' => 'required',
            'offlineUploadFile' => 'required|file',
            'offlineDescription' => 'required',
        ]);

        $checkAlreadyRequest = OfflinePlanChange::where('society_id', $this->society->id)
            ->where('status', 'pending')
            ->exists();

        if ($checkAlreadyRequest) {
            $this->alert('error', __('messages.alreadyRequestPending'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        $package = Package::findOrFail($this->selectedPlan->id);
        $packageType = $package->package_type == PackageType::LIFETIME ? 'lifetime' : ($this->isAnnual ? 'annual' : 'monthly');

        $offlinePlanChange = new OfflinePlanChange();
        $offlinePlanChange->package_id = $this->selectedPlan->id;
        $offlinePlanChange->package_type = $packageType;
        $offlinePlanChange->society_id = $this->society->id;
        $offlinePlanChange->offline_method_id = $this->offlineMethodId;
        $offlinePlanChange->description = $this->offlineDescription;
        $offlinePlanChange->amount = $package->package_type == PackageType::LIFETIME ? $package->price : ($this->isAnnual ? $package->annual_price : $package->monthly_price);
        $offlinePlanChange->pay_date = now()->format('Y-m-d');
        $offlinePlanChange->next_pay_date = $package->package_type == PackageType::LIFETIME ? null : ($this->isAnnual ? now()->addYear()->format('Y-m-d') : now()->addMonth()->format('Y-m-d'));

        if ($this->offlineUploadFile) {
            $offlinePlanChange->file_name = Files::uploadLocalOrS3($this->offlineUploadFile, OfflinePlanChange::FILE_PATH);
        }

        $offlinePlanChange->save();

        // Send email to superAdmin to review offline payment request
        $superAdmin = User::withoutGlobalScopes()->whereNull('society_id')->first();
        Notification::send($superAdmin, new SocietyPlanModificationRequest($this->society, $offlinePlanChange));

        $this->showPaymentMethodModal = false;
        $this->reset(['offlineMethodId', 'offlineUploadFile', 'offlineDescription', 'selectedPlan']);

        $this->alert('success', __('messages.requestSubmittedSuccessfully'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
        $this->redirect(route('settings.index', ['tab' => 'billing', 'offlineRequest' => 'offlineRequest']), navigate: true);
    }

    // Loading available packages based on selected currency
    public function updatedSelectedCurrency()
    {
        $this->loadAvailablePackages();
    }

    // Razorpay Subscription Initiate
    public function razorpaySubscription($planId)
    {
        $plan = Package::find($planId);
        $credential = SuperadminPaymentGateway::first();

        if (!$plan || !$credential) {
            return $this->showError(__('messages.noPlanIdFound'));
        }

        $apiKey = $credential->razorpay_key;
        $apiSecret = $credential->razorpay_secret;

        $api = new Api($apiKey, $apiSecret);
        $societyId = society()->id;
        $amount = $plan->price ?? 0;
        $currencyCode = $plan->currency->currency_code;
        $currency_id = $plan->currency_id;
        $type = $this->isAnnual ? 'annual' : 'monthly';

        try {
            if ($plan->package_type == PackageType::LIFETIME) {
                return $this->processLifetimePayment($apiKey,$api, $plan, $amount, $currencyCode, $currency_id, $societyId);
            } else {
                return $this->processSubscription($apiKey ,$api, $plan, $societyId, $currencyCode, $type);
            }
        } catch (\Exception $e) {
            return $this->showError($e->getMessage());
        }
    }

    private function processLifetimePayment($apiKey ,$api, $plan, $amount, $currencyCode, $currency_id, $societyId)
    {
        $societyPayment = SocietyPayment::create([
            'society_id' => $societyId,
            'amount' => $amount,
            'package_id' => $plan->id,
            'package_type' => $plan->package_type,
            'currency_id' => $currency_id,
        ]);

        $razorpayOrder = $api->order->create([
            'amount' => ($amount * 100),
            'currency' => $currencyCode
        ]);

        $societyPayment->update(['razorpay_order_id' => $razorpayOrder->id]);

        return $this->dispatchRazorpay($apiKey, $razorpayOrder->id, $plan, $amount, $currencyCode, $societyId, $plan->package_type);
    }

    // Process Subscription for Razorpay
    private function processSubscription($apiKey, $api, $plan, $societyId, $currencyCode, $type)
    {

        $planID = $type == 'annual' ? $plan->razorpay_annual_plan_id : $plan->razorpay_monthly_plan_id;

        if (!$planID) {
            return $this->showError(__('messages.noPlanIdFound'));
        }

        $subscription = $api->subscription->create([
            'plan_id' => $planID,
            'customer_notify' => 1,
            'total_count' => 100
        ]);

        return $this->dispatchRazorpay($apiKey , $subscription->id, $plan, null, $currencyCode, $societyId, $type);
    }

    // Dispatch Razorpay Payment
    private function dispatchRazorpay($key, $id, $plan, $amount, $currencyCode, $societyId, $packageType)
    {
        $data = [
            'key' => $key,
            'name' => global_setting()->name,
            'description' => $plan->description,
            'image' => global_setting()->logo_url,
            'currency' => $currencyCode,
            'order_id' => $amount ? $id : null,
            'subscription_id' => !$amount ? $id : null,
            'amount' => $amount,
            'notes' => [
                'package_id' => $plan->id,
                'package_type' => $packageType,
                'society_id' => $societyId,
            ]
        ];

        $this->dispatch('initiateRazorpay', json_encode($data));
    }

    // Show Error
    private function showError($message)
    {
        $this->alert('error', __($message), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }


    // Razorpay Payment Confirmation
    #[On('confirmRazorpayPayment')]
    public function confirmRazorpayPayment($payment_id, $reference_id, $signature)
    {
        $credential = SuperadminPaymentGateway::first();

        if (!$credential) {
            session()->put('error', __('messages.noCredentialFound'));
        }

        $paymentId = $payment_id;
        $referenceId = $reference_id;
        $isOrderId = strpos($referenceId, 'order_') === 0;
        $isSubscriptionId = strpos($referenceId, 'sub_') === 0;
        $apiKey = $credential->razorpay_key;
        $secretKey = $credential->razorpay_secret;

        if (!$isOrderId && !$isSubscriptionId) {
            session()->put('error', __('messages.invalidReferenceId'));
        }

        $api = new Api($apiKey, $secretKey);

        if ($isOrderId) {
            $societyPayment = SocietyPayment::where('razorpay_order_id', $referenceId)
                ->where('status', 'pending')
                ->first();
        } else {
            $expectedSignature = hash_hmac('sha256', $paymentId . '|' . $referenceId, $secretKey);
            if ($expectedSignature !== $signature) {
                session()->put('error', __('messages.invalidSignature'));
                return;
            }

            $societyPayment = null;
        }

        try {
            DB::beginTransaction(); // Prevent partial updates
            // Fetch Payment Details
            $payment = $api->payment->fetch($paymentId);
            $plan = Package::findOrFail($payment->notes->package_id);
            $society = $this->society;
            $packageType = $payment->notes->package_type;
            $currencyCode = $plan->currency->currency_code;

            // Capture Payment if authorized
            if ($payment->status == 'authorized') {
                $payment->capture(['amount' => $payment->amount, 'currency' => $currencyCode]);
            }

            // Update Society Subscription
            $society->update([
                'package_id' => $plan->id,
                'package_type' => $packageType,
                'trial_ends_at' => null,
                'is_active' => true,
                'status' => 'active',
                'license_expire_on' => null,
                'license_updated_at' => now()->format('Y-m-d'),
            ]);

            // Deactivate previous subscriptions
            GlobalSubscription::where('society_id', $society->id)
                ->where('subscription_status', 'active')
                ->update(['subscription_status' => 'inactive']);

            // Create New Subscription Entry
            $subscription = GlobalSubscription::create([
                'society_id' => $society->id,
                'package_type' => $packageType,
                'transaction_id' => $paymentId,
                'currency_id' => $plan->currency_id,
                'razorpay_id' => $packageType == 'annual' ? $plan->razorpay_annual_plan_id : $plan->razorpay_monthly_plan_id,
                'razorpay_plan' => $packageType,
                'quantity' => 1,
                'package_id' => $plan->id,
                'subscription_id' => ($packageType == 'lifetime') ? $payment->order_id : $referenceId,
                'gateway_name' => 'razorpay',
                'subscription_status' => 'active',
                'ends_at' => $society->license_expire_on ?? null,
                'subscribed_on_date' => now(),
            ]);

            // Generate Invoice
            GlobalInvoice::create([
                'society_id' => $society->id,
                'currency_id' => $subscription->currency_id,
                'package_id' => $subscription->package_id,
                'global_subscription_id' => $subscription->id,
                'transaction_id' => $subscription->transaction_id,
                'invoice_id' => $payment->invoice_id ?? null,
                'subscription_id' => $subscription->subscription_id,
                'package_type' => $subscription->package_type,
                'plan_id' => $subscription->razorpay_id,
                'amount' => $payment->amount / 100,
                'total' => $payment->amount / 100,
                'pay_date' => now()->format('Y-m-d H:i:s'),
                'next_pay_date' => ($packageType == 'annual') ? now()->addYear() : now()->addMonth(),
                'gateway_name' => 'razorpay',
                'status' => 'active',
            ]);

            DB::commit(); // Commit transaction

            if ($societyPayment) {
                $societyPayment->razorpay_payment_id = $paymentId;
                $societyPayment->status = 'paid';
                $societyPayment->payment_date_time = now()->toDateTimeString();
                $societyPayment->razorpay_signature = $signature;
                $societyPayment->transaction_id = $paymentId;
                $societyPayment->save();
            }

            // Notify Admin & Society
            $superadmin = User::withoutGlobalScopes()->whereNull('society_id')->first();
            Notification::send($superadmin, new SocietyUpdatedPlan($society, $subscription->package_id));

            $societyAdmin = $society->societyAdmin($society);
            Notification::send($societyAdmin, new SocietyUpdatedPlan($society, $subscription->package_id));

            // Clear session & Redirect
            session()->forget('society');
            session()->flash('flash.banner', __('messages.planUpgraded'));
            session()->flash('flash.bannerStyle', 'success');
            session()->flash('flash.link', route('settings.index', ['tab' => 'billing']));
            return $this->redirect(route('dashboard'), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            session()->put('error', $e->getMessage());
            return Reply::redirect(route('pricing.plan'));
        }
    }


    // Loading Packages with currency and filters
    private function loadAvailablePackages()
    {
        if (!$this->selectedCurrency) {
            $this->packages = collect();
            return;
        }

        // Determine subscription status field based on isAnnual
        $statusField = $this->isAnnual ? 'annual_status' : 'monthly_status';

        // Build query
        $this->packages = Package::with(['currency', 'modules'])
            ->where('is_private', 0) // Non-private packages only
            ->where(function ($query) use ($statusField) {
                $query->where('package_type', 'lifetime')
                    ->orWhere('package_type', 'default') // Default packages
                    ->orWhere('is_free', true) // Include free packages
                    ->orWhere(function ($query) use ($statusField) {
                        $query->where('package_type', 'standard')
                        ->where($statusField, true); // Standard packages with the relevant status
                    });
            })
            ->where('package_type', '!=', 'trial') // Exclude trial packages
            ->where(function ($query) {
                $query->where('currency_id', $this->selectedCurrency)
                ->orWhere('package_type', 'default') // Default packages ignore currency
                ->orWhere('is_free', true); // Free packages ignore currency
            })->orderBy('sort_order')
            ->get();
    }

    // toggle Monthly and Annual
    public function toggle()
    {
        $this->isAnnual = !$this->isAnnual;
        $this->loadAvailablePackages(); // Refresh data on toggle
    }

    // initiate stripe payment
    public function initiateStripePayment()
    {
        if ($this->selectedPlan->package_type == PackageType::LIFETIME) {
            $amount = $this->selectedPlan->price;
        } else {
            $amount = $this->isAnnual ? $this->selectedPlan->annual_price : $this->selectedPlan->monthly_price;
        }

        if (!$amount) {
            $this->alert('error', __('messages.noPlanIdFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        $plan = Package::find($this->selectedPlan->id);
        $type = $this->isAnnual ? 'annual' : 'monthly';
        $currency_id = $plan->currency_id;

        $payment = SocietyPayment::create([
            'society_id' => $this->society->id,
            'amount' => $amount,
            'package_id' => $plan->id,
            'package_type' => $type,
            'currency_id' => $currency_id,
        ]);

        $this->dispatch('stripePlanPaymentInitiated', payment: $payment);
    }


    public function freePlan()
    {
        $package = Package::findOrFail($this->selectedPlan->id);
        $currencyId = $package->currency_id ?: global_setting()->currency_id;
        $society = $this->society;
        $type = $this->isAnnual ? 'annual' : 'monthly';

        GlobalSubscription::where('society_id', $society->id)
            ->where('subscription_status', 'active')
            ->update(['subscription_status' => 'inactive']);

        $subscription = new GlobalSubscription();
        $subscription->society_id = $society->id;
        $subscription->package_id = $package->id;
        $subscription->currency_id = $currencyId;
        $subscription->package_type = $type;
        $subscription->quantity = 1;
        $subscription->gateway_name = 'offline';
        $subscription->subscription_status = 'active';
        $subscription->subscribed_on_date = now();
        $subscription->transaction_id = str(str()->random(15))->upper();
        $subscription->save();

        // create offline invoice
        $offlineInvoice = new GlobalInvoice();
        $offlineInvoice->global_subscription_id = $subscription->id;
        $offlineInvoice->society_id = $society->id;
        $offlineInvoice->currency_id = $currencyId;
        $offlineInvoice->package_id = $package->id;
        $offlineInvoice->package_type = $type;
        $offlineInvoice->total = 0;
        $offlineInvoice->pay_date = now()->format('Y-m-d');
        $offlineInvoice->next_pay_date = $type == 'annual' ? now()->addYear()->format('Y-m-d') : now()->addMonth()->format('Y-m-d');
        $offlineInvoice->gateway_name = 'offline';
        $offlineInvoice->transaction_id = $subscription->transaction_id;
        $offlineInvoice->save();

        // Change society package
        $society->package_id = $package->id;
        $society->package_type = $type;
        $society->status = 'active';
        $society->license_expire_on = $type == 'annual' ? now()->addYear()->format('Y-m-d') : now()->addMonth()->format('Y-m-d');
        $society->license_updated_at = now()->format('Y-m-d');
        $society->save();

        // Send superadmin notification
        $generatedBy = User::withoutGlobalScopes()->whereNull('society_id')->first();
        Notification::send($generatedBy, new SocietyUpdatedPlan($society, $subscription->package_id));

        // Send notification to society admin
        $societyAdmin = $society->societyAdmin($society);
        Notification::send($societyAdmin, new SocietyUpdatedPlan($society, $subscription->package_id));

        session()->forget('society');


        request()->session()->flash('flash.banner', __('messages.planUpgraded'));
        request()->session()->flash('flash.bannerStyle', 'success');
        request()->session()->flash('flash.link', route('settings.index', ['tab' => 'billing']));
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function togglePaymentOptions($value)
    {
        $this->showOnline = (bool) $value;

        if ($this->showOnline) {
            $this->reset(['offlineMethodId']);
        }
    }

    public function initiateFlutterwavePayment()
    {
        $plan = Package::find($this->selectedPlan->id);
        $type = $plan->package_type === PackageType::LIFETIME ? 'lifetime' : ($this->isAnnual ? 'annual' : 'monthly');
        $currency_id = $plan->currency_id;
        if ($plan->package_type == PackageType::LIFETIME) {
            $amount = $plan->price;
        } else {
            $amount = $this->isAnnual ? $this->selectedPlan->annual_price : $this->selectedPlan->monthly_price;
        }

        if (!$amount) {
            $this->alert('error', __('messages.noPlanIdFound'));
            return;
        }

        $payment = SocietyPayment::create([
            'society_id' => $this->society->id,
            'amount' => $amount,
            'package_id' => $plan->id,
            'package_type' => $type,
            'currency_id' => $currency_id,
        ]);

        $params = [
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => $plan->currency->currency_code,
            'society_id' => $this->society->id,
            'package_id' => $plan->id,
            'package_type' => $type,
            'email' => $this->society->email,
        ];
        
        // Add these parameters to the dispatch
        $this->dispatch('redirectToFlutterwave', ['params' => $params]);
    }

    public function initiatePaypalPayment()
    {
        $plan = Package::find($this->selectedPlan->id);
        $type = $this->isAnnual ? 'annual' : 'monthly';
        $currency_id = $plan->currency_id;

        if ($this->selectedPlan->package_type == PackageType::LIFETIME) {
            $amount = $this->selectedPlan->price;
        } else {
            $amount = $this->isAnnual ? $this->selectedPlan->annual_price : $this->selectedPlan->monthly_price;
        }

        if (!$amount) {
            $this->alert('error', __('messages.noPlanIdFound'));
            return;
        }

        $payment = SocietyPayment::create([
            'society_id' => $this->society->id,
            'amount' => $amount,
            'package_id' => $plan->id,
            'package_type' => $type,
            'currency_id' => $currency_id,
        ]);

        $params = [
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => $plan->currency->currency_code,
            'society_id' => $this->society->id,
            'package_id' => $plan->id,
            'package_type' => $type,
        ];

        $this->dispatch('redirectToPaypal', ['params' => $params]);
    }

    public function initiatePaystackPayment()
    {
        $plan = Package::find($this->selectedPlan->id);
        $type = $plan->package_type === PackageType::LIFETIME ? 'lifetime' : ($this->isAnnual ? 'annual' : 'monthly');
        $currency_id = $plan->currency_id;

        if ($plan->package_type == PackageType::LIFETIME) {
            $amount = $plan->price;
        } else {
            $amount = $this->isAnnual ? $plan->annual_price : $plan->monthly_price;
        }

        if (!$amount) {
            $this->alert('error', __('messages.noPlanIdFound'));
            return;
        }

        $payment = SocietyPayment::create([
            'society_id' => $this->society->id,
            'amount' => $amount,
            'package_id' => $plan->id,
            'package_type' => $type,
            'currency_id' => $currency_id,
        ]);

        $params = [
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => $plan->currency->currency_code,
            'society_id' => $this->society->id,
            'package_id' => $plan->id,
            'package_type' => $type
        ];

        // Trigger frontend to redirect to Paystack
        $this->dispatch('redirectToPaystack', ['params' => $params]);
    }

    public function initiatePayfastPayment()
    {
        $plan = Package::find($this->selectedPlan->id);
        $type = $plan->package_type === PackageType::LIFETIME ? 'lifetime' : ($this->isAnnual ? 'annual' : 'monthly');
        $currency_id = $plan->currency_id;

        if ($plan->package_type == PackageType::LIFETIME) {
            $amount = $plan->price;
        } else {
            $amount = $this->isAnnual ? $plan->annual_price : $plan->monthly_price;
        }

        if (!$amount) {
            $this->alert('error', __('messages.noPlanIdFound'));
            return;
        }

        $payment = SocietyPayment::create([
            'society_id' => $this->society->id,
            'amount' => $amount,
            'package_id' => $plan->id,
            'package_type' => $type,
            'currency_id' => $currency_id,
        ]);

        $params = [
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => $plan->currency->currency_code,
            'society_id' => $this->society->id,
            'package_id' => $plan->id,
            'package_type' => $type
        ];

        $this->dispatch('redirectToPayfast', ['params' => $params]);
    }

    public function render()
    {
        return view('livewire.plan.plan-list');
    }
}
