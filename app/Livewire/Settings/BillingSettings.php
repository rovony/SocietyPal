<?php

namespace App\Livewire\Settings;

use App\Models\Package;
use App\Models\Society;
use Livewire\Component;
use App\Enums\PackageType;
use Livewire\WithPagination;
use App\Models\GlobalInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OfflinePlanChange;
use App\Models\GlobalSubscription;
use Illuminate\Support\Facades\Http;
use Unicodeveloper\Paystack\Paystack;
use App\Models\SuperadminPaymentGateway;
use App\Traits\SuperAdmin\PaystackSettings;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Config;
use stdClass;
use GuzzleHttp\Client;
use Carbon\Carbon;

class BillingSettings extends Component
{
    use WithPagination, LivewireAlert;
    use PaystackSettings;

    public $currentTab;
    public $activeSetting;
    public $currentPackageName;
    public $currentPackageType;
    public $licenseExpireOn;
    public $currentPackageFeatures = [];
    public $nextPaymentDate;
    public function mount()
    {
        $this->showTab('planDetails');
        $society = Society::where('id', society()->id)->first();
        $this->currentPackageName = $society->package->package_name;
        $this->currentPackageFeatures = json_decode($society->package->additional_features, true) ?: [];
        $this->currentPackageType = __('modules.billing.' . $society->package->package_type->value);
        $this->licenseExpireOn = $society->package->package_type->value !== 'lifetime'
        ? optional($society->license_expire_on)->format('d F, Y')
            : __('modules.package.lifetime');

        $this->nextPaymentDate = GlobalInvoice::where('society_id', $society->id)
            ->where('status', 'active')
            ->whereNotNull('next_pay_date')
            ->orderByDesc('id')
            ->value('next_pay_date');

            $this->nextPaymentDate = optional($this->nextPaymentDate)->format('d F, Y');

        if ($society->package_type) {
            $this->currentPackageType .= ' (' . __('modules.billing.' . $society->package_type) . ')';
        }

    }

    public function showTab($tab)
    {
        $this->currentTab = $tab;
        $this->activeSetting = $this->currentTab;
    }

    public function downloadReceipt($id)
    {
        $invoice = GlobalInvoice::findOrFail($id);

        if (!$invoice) {

            $this->alert('error', __('messages.noInvoiceFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            return;
        }


        $pdf = Pdf::loadView('billing.billing-receipt', ['invoice' => $invoice]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'billing-receipt-' . uniqid() . '.pdf');
    }

    public function cancelSubscription($cancelType = false)
    {
        $subscription = GlobalSubscription::where('society_id', society()->id)
            ->where('subscription_status', 'active')
            ->latest()
            ->first();

        $subscriptionId = $subscription->subscription_id;
        $gatewayName = $subscription->gateway_name;

        if (!$subscriptionId) {
            $this->alert('error', __('messages.noSubscriptionFound'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        $paymentGateway = SuperadminPaymentGateway::first();

        if ($paymentGateway) {
            try {
                match ($gatewayName) {
                    'stripe' => $this->cancelStripeSubscription($subscriptionId, $cancelType, $paymentGateway->stripe_secret),
                    'razorpay' => $this->cancelRazorpaySubscription($subscriptionId, $cancelType, $paymentGateway->razorpay_key, $paymentGateway->razorpay_secret),
                    'flutterwave' => $this->cancelFlutterwaveSubscription($subscriptionId, $cancelType, $paymentGateway->flutterwave_secret),
                    'paystack' => $this->cancelPaystackSubscription($subscriptionId, $cancelType, $paymentGateway->paystack_secret),
                    'paypal' => $this->cancelPaypalSubscription($subscriptionId, $cancelType, $paymentGateway),
                    'payfast' => $this->cancelPayfastSubscription($subscriptionId, $cancelType, $paymentGateway),
                    default => session()->flash('error', __('messages.invalidGateway')),
                };
            } catch (\Exception $e) {
                session()->flash('error', $gatewayName . ' Error: ' . $e->getMessage());
            }
        }
    }

    private function cancelStripeSubscription($subscriptionId, $cancelType, $stripeSecret)
    {
        $stripe = new \Stripe\StripeClient($stripeSecret);
        $society = Society::where('id', society()->id)->first();
        if ($cancelType) {
            $stripe->subscriptions->cancel($subscriptionId);
            $this->updateSubscription($society);
        } else {
            $stripe->subscriptions->update($subscriptionId, [
                'cancel_at_period_end' => true,
            ]);

            $society->license_expire_on = \Carbon\Carbon::createFromTimestamp($stripe->subscriptions->retrieve($subscriptionId)->current_period_end)->format('Y-m-d');
            $society->save();
        }

        $this->alert('success', __('messages.subscriptionCancelled'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->js("Livewire.navigate(window.location.href)");
    }

    private function cancelRazorpaySubscription($subscriptionId, $cancelType, $razorpayKey, $razorpaySecret)
    {
        $api = new \Razorpay\Api\Api($razorpayKey, $razorpaySecret);

        $subscription = $api->subscription->fetch($subscriptionId);

        $subscription->cancel([
            'cancel_at_cycle_end' => $cancelType ? 0 : 1
        ]);

        $society = Society::where('id', society()->id)->first();

        if ($cancelType) {
            $this->updateSubscription($society);
        } else {
            $society->license_expire_on = \Carbon\Carbon::createFromTimestamp($subscription->current_end)->format('Y-m-d');
            $society->save();
        }

        $this->alert('success', __('messages.subscriptionCancelled'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->js("Livewire.navigate(window.location.href)");
    }

    public function updateSubscription(Society $society)
    {
        $package = Package::where('package_type', PackageType::DEFAULT)->first();

        $currencyId = $package->currency_id ?: global_setting()->currency_id;


        $expireDate = now()->addMonth();

        $society->package_id = $package->id;
        $society->package_type = 'monthly';
        $society->license_expire_on = $expireDate;
        $society->status = 'active';
        $society->save();

        GlobalSubscription::where('society_id', $society->id)
            ->where('subscription_status', 'active')
            ->update(['subscription_status' => 'inactive']);

        $subscription = new GlobalSubscription();
        $subscription->society_id = $society->id;
        $subscription->package_id = $society->package_id;
        $subscription->currency_id = $currencyId;
        $subscription->package_type = $society->package_type;
        $subscription->quantity = 1;
        $subscription->gateway_name = 'offline';
        $subscription->subscription_status = 'active';
        $subscription->subscribed_on_date = now();
        $subscription->ends_at = $society->license_expire_on;
        $subscription->transaction_id = str(str()->random(15))->upper();
        $subscription->save();

        $offlineInvoice = new GlobalInvoice();
        $offlineInvoice->global_subscription_id = $subscription->id;
        $offlineInvoice->society_id = $subscription->society_id;
        $offlineInvoice->currency_id = $subscription->currency_id;
        $offlineInvoice->package_id = $subscription->package_id;
        $offlineInvoice->package_type = $subscription->package_type;
        $offlineInvoice->total = 0.00;
        $offlineInvoice->pay_date = now();
        $offlineInvoice->next_pay_date = $subscription->ends_at;
        $offlineInvoice->gateway_name = 'offline';
        $offlineInvoice->transaction_id = $subscription->transaction_id;
        $offlineInvoice->save();
    }

    private function cancelFlutterwaveSubscription($subscriptionId, $cancelType, $flutterwaveSecret)
    {
        try {

            $response = Http::withToken($flutterwaveSecret)
                ->put("https://api.flutterwave.com/v3/subscriptions/{$subscriptionId}/cancel");

            if ($response->successful() && $response->json('status') === 'success') {
                $society = Society::find(society()->id);

                if ($cancelType) {
                    $this->updateSubscription($society);
                } else {
                    $licenseDuration = $society->package_type === 'monthly' ? 'addMonth' : 'addYear';
                    if ($society->license_updated_at) {
                        $society->license_expire_on = \Carbon\Carbon::parse($society->license_updated_at)->$licenseDuration();
                        $society->save();
                    } else {
                        $this->alert('error', __('messages.invalidLicenseDate'));
                    }
                }

                $this->alert('success', __('messages.subscriptionCancelled'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
            } else {
                // Show error alert if the cancellation fails
                $this->alert('error', __('messages.cancelFailed') . ': ' . $response->json('message'));
            }
        } catch (\Exception $e) {
            // Handle exceptions and show error alert
            $this->alert('error', __('messages.errorOccurred') . ': ' . $e->getMessage());
        }

        // Refresh the page after the operation
        $this->js("Livewire.navigate(window.location.href)");
    }

    private function cancelPaystackSubscription($subscriptionId, $cancelType, $paystackSecret)
    {
        try {
            // Fetch the token from your DB — make sure you're storing it when creating the subscription
            $this->setPaystackConfigs();
            $subscription = GlobalSubscription::where('subscription_id', $subscriptionId)->first();
            if (!$subscription || !$subscription->token) {
                $this->alert('error', __('messages.missingEmailToken'));
                return;
            }   
            //  Merge the required request data
            request()->merge([
                'code' => $subscription->subscription_id, 
                'token' => $subscription->token,  
            ]);
    
            // Call the Paystack wrapper method
            $paystack = new Paystack();
            $response = $paystack->disableSubscription();

            if (isset($response['status']) && $response['status'] === true) {
                $society = Society::find(society()->id);
    
                if ($cancelType) {
                    $this->updateSubscription($society);
                } else {
                    $licenseDuration = $society->package_type === 'monthly' ? 'addMonth' : 'addYear';
                    if ($society->license_updated_at) {
                        $society->license_expire_on = \Carbon\Carbon::parse($society->license_updated_at)->$licenseDuration();
                        $society->save();
                    } else {
                        $this->alert('error', __('messages.invalidLicenseDate'));
                    }
                }
    
                $this->alert('success', __('messages.subscriptionCancelled'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
            } else {
                $this->alert('error', __('messages.cancelFailed') . ': ' . ($response['message'] ?? 'Unknown error'));
            } 
        } catch (\Exception $e) {
            logger()->error('Exception in cancelling Paystack subscription', ['error' => $e->getMessage()]);
            $this->alert('error', __('messages.errorOccurred') . ': ' . $e->getMessage());
        }
    
        $this->js("Livewire.navigate(window.location.href)");
    }

    private function cancelPaypalSubscription($subscriptionId, $cancelType, $paymentGateway)
    {
        $society = Society::where('id', society()->id)->first();
        $paypalClientId = $paymentGateway->paypal_mode === 'sandbox' ? $paymentGateway->test_paypal_client_id : $paymentGateway->live_paypal_client_id;
        $paypalSecret = $paymentGateway->paypal_mode === 'sandbox' ? $paymentGateway->test_paypal_secret : $paymentGateway->live_paypal_secret;

        try {
            $apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential($paypalClientId, $paypalSecret)
            );
            config(['paypal.settings.mode' => $paymentGateway->paypal_mode]);
            $paypal_conf = Config::get('paypal');
            $apiContext->setConfig($paypal_conf['settings']);

            $agreement = new \PayPal\Api\Agreement();
            $agreement->setId($subscriptionId);

            $descriptor = new \PayPal\Api\AgreementStateDescriptor();
            $descriptor->setNote('Cancelling the agreement via admin panel');

            // Cancel the agreement
            $agreement->cancel($descriptor, $apiContext);
            $cancelledAgreement = \PayPal\Api\Agreement::get($agreement->getId(), $apiContext);

            if ($cancelledAgreement->getState() === 'Cancelled') {
                $endOn = \Carbon\Carbon::parse($cancelledAgreement->getAgreementDetails()->getFinalPaymentDate())->format('Y-m-d');

                if ($cancelType) {
                    $this->updateSubscription($society);
                } else {
                    $society->license_expire_on = $endOn;
                    $society->save();
                }

                $this->alert('success', __('messages.subscriptionCancelled'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
            } else {
                $this->alert('error', __('messages.cancelFailed'));
            }

        } catch (\Exception $e) {
            logger()->error('PayPal cancellation error: ' . $e->getMessage());
            $this->alert('error', __('messages.errorOccurred') . ': ' . $e->getMessage());
        }

        $this->js("Livewire.navigate(window.location.href)");
    }

    private function cancelPayfastSubscription($subscriptionId, $cancelType, $paymentGateway)
    {
        $credential = new stdClass();
        if($paymentGateway->payfast_mode == 'sandbox'){
            $credential->payfast_salt_passphrase = $paymentGateway->test_payfast_passphrase;
            $credential->payfast_key = $paymentGateway->test_payfast_merchant_id;
            $credential->payfast_secret = $paymentGateway->test_payfast_merchant_key;
            $cancelSandbox = '?testing=true';

        }
        else{
            $credential->payfast_salt_passphrase = $paymentGateway->payfast_passphrase;
            $credential->payfast_key = $paymentGateway->payfast_merchant_id;
            $credential->payfast_secret = $paymentGateway->payfast_merchant_key;
            $cancelSandbox = '';
        }

        $payfastInvoice = GlobalInvoice::where('gateway_name', 'payfast')->latest()->first();
        $date = now()->format('Y-m-d\TH:i:s');
        
             try{
                $url = 'https://api.payfast.co.za/subscriptions/'.$payfastInvoice->token.'/cancel'.$cancelSandbox;

                $header = ['merchant-id' => $credential->payfast_key, 'version' => 'v1' , 'timestamp' => $date, 'signature' => $payfastInvoice->signature];
                $client = new Client();
                $res = $client->request('PUT', $url, ['headers' => $header]);

                $conversionRate = $res->getBody();
                $conversionRate = json_decode($conversionRate, true);
                if($conversionRate['status'] == 'success'){
                     $society = Society::find(society()->id);
                    if ($cancelType) {
                        $this->updateSubscription($society);
                    } else {
                        $licenseDuration = $society->package_type === 'monthly' ? 'addMonth' : 'addYear';

                        if ($society->license_updated_at) {
                            $society->license_expire_on = Carbon::parse($society->license_updated_at)->$licenseDuration();
                            $society->save();
                        } else {
                            $this->alert('error', __('messages.invalidLicenseDate'));
                        }
                    }

                    $this->alert('success', __('messages.subscriptionCancelled'), [
                        'toast' => true,
                        'position' => 'top-end',
                        'showCancelButton' => false,
                        'cancelButtonText' => __('app.close')
                    ]);
                }else {
                    $this->alert('error', __('messages.cancelFailed') . ': ' . ($responseBody['message'] ?? 'Unknown error'));
                }

            } 
            catch (\Exception $e) {
                logger()->error('Payfast cancellation error: ' . $e->getMessage());
                $this->alert('error', __('messages.errorOccurred') . ': ' . $e->getMessage());
            }

        $this->js("Livewire.navigate(window.location.href)");
    }

    public function render()
    {
        $invoices = GlobalInvoice::where('society_id', society()->id)
            ->orderByDesc('id')
            ->paginate(10);

        $offlinePaymentRequest = OfflinePlanChange::where('society_id', society()->id)->paginate(10);

        return view('livewire.settings.billing-settings', [
            'offlinePaymentRequest' => $offlinePaymentRequest,
            'invoices' => $invoices
        ]);
    }
}
