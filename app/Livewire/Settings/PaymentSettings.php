<?php

namespace App\Livewire\Settings;

use App\Models\PaymentGatewayCredential;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Helper\Files;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use PhpParser\Node\Expr\Cast\Bool_;

class PaymentSettings extends Component
{

    use LivewireAlert, WithFileUploads;

    public $razorpaySecret;
    public $razorpayKey;
    public $razorpayWebhookKey;
    public $razorpayStatus;
    public $isRazorpayEnabled;
    public $isStripeEnabled;
    public $offlinePaymentMethod;
    public $paymentGateway;
    public $stripeSecret;
    public $activePaymentSetting = 'razorpay';
    public $stripeKey;
    public $stripeWebhookKey;
    public bool $stripeStatus;
    public $enableCashPayment = false;
    public $enableQrPayment = false;
    public $paymentDetails;
    public $qrCodeImage;
    public $isofflinepaymentEnabled;
    public bool $enablePayViaCash;
    public $webhookUrl;
    public $flutterwaveMode;
    public $flutterwaveStatus;
    public $liveFlutterwaveKey;
    public $liveFlutterwaveSecret;
    public $liveFlutterwaveHash;
    public $testFlutterwaveKey;
    public $testFlutterwaveSecret;
    public $testFlutterwaveHash;
    public $flutterwaveWebhookSecretHash;
    public $isFlutterwaveEnabled;
    public $paystackKey;
    public $paystackSecret;
    public $paystackMerchantEmail;
    public $paystackStatus;
    public $isPaystackEnabled;
    public $paystackMode;
    public $isPaypalEnabled;
    public $paypalStatus;
    public $paypalMode;
    public $sandboxPaypalClientId;
    public $sandboxPaypalSecret;
    public $livePaypalClientId;
    public $livePaypalSecret;
    public $webhookRoute;
    public $testPaystackKey;
    public $testPaystackSecret;
    public $testPaystackMerchantEmail;
    public $payfastMerchantId;
    public $payfastMerchantKey;
    public $payfastPassphrase;
    public $payfastMode;
    public $payfastStatus;
    public $testPayfastMerchantId;
    public $testPayfastMerchantKey;
    public $testPayfastPassphrase;
    public $isPayfastEnabled;



    public function mount()
    {
        $this->paymentGateway = PaymentGatewayCredential::first();
        $acceptMaintenancePayment = in_array('Accept Maintenance Payment', society_modules());

        if (!$acceptMaintenancePayment) {
            $this->enablePayViaCash = true;
            $this->activePaymentSetting = 'offline';
            if ($this->paymentGateway) {
                $this->paymentGateway->update(['is_cash_payment_enabled' => true]);
            }
        }
        $this->setCredentials();
    }

    public function activeSetting($tab)
    {
        $this->activePaymentSetting = $tab;
        $this->setCredentials();
    }

    private function setCredentials()
    {
        $acceptMaintenancePayment = in_array('Accept Maintenance Payment', society_modules());

        if (!$acceptMaintenancePayment) {

            $this->enablePayViaCash = true;
            $this->activePaymentSetting = 'offline';
        } else {
            $this->razorpayKey = $this->paymentGateway->razorpay_key;
            $this->razorpaySecret = $this->paymentGateway->razorpay_secret;
            $this->razorpayWebhookKey = $this->paymentGateway->razorpay_webhook_key;
            $this->razorpayStatus = (bool)$this->paymentGateway->razorpay_status;

            $this->stripeKey = $this->paymentGateway->stripe_key;
            $this->stripeSecret = $this->paymentGateway->stripe_secret;
            $this->stripeWebhookKey = $this->paymentGateway->stripe_webhook_key;
            $this->stripeStatus = (bool)$this->paymentGateway->stripe_status;

            $this->isRazorpayEnabled = $this->paymentGateway->razorpay_status;
            $this->isStripeEnabled = $this->paymentGateway->stripe_status;

            $this->flutterwaveMode = $this->paymentGateway->flutterwave_mode;
            $this->flutterwaveStatus = (bool)$this->paymentGateway->flutterwave_status;
            $this->liveFlutterwaveKey = $this->paymentGateway->live_flutterwave_key;
            $this->liveFlutterwaveSecret = $this->paymentGateway->live_flutterwave_secret;
            $this->liveFlutterwaveHash = $this->paymentGateway->live_flutterwave_hash;
            $this->testFlutterwaveKey = $this->paymentGateway->test_flutterwave_key;
            $this->testFlutterwaveSecret = $this->paymentGateway->test_flutterwave_secret;
            $this->testFlutterwaveHash = $this->paymentGateway->test_flutterwave_hash;
            $this->isFlutterwaveEnabled = $this->paymentGateway->flutterwave_status;
            $this->flutterwaveWebhookSecretHash = $this->paymentGateway->flutterwave_webhook_secret_hash;

            $this->isPaypalEnabled = $this->paymentGateway->paypal_status;
            $this->paypalStatus = (bool) $this->paymentGateway->paypal_status;
            $this->paypalMode = $this->paymentGateway->paypal_mode;
            $this->sandboxPaypalClientId = $this->paymentGateway->sandbox_paypal_client_id;
            $this->sandboxPaypalSecret = $this->paymentGateway->sandbox_paypal_secret;
            $this->livePaypalClientId = $this->paymentGateway->paypal_client_id ;
            $this->livePaypalSecret = $this->paymentGateway->paypal_secret ;

            $this->paystackStatus = (bool)$this->paymentGateway->paystack_status;
            $this->paystackKey = $this->paymentGateway->paystack_key;
            $this->paystackSecret = $this->paymentGateway->paystack_secret;
            $this->paystackMerchantEmail = $this->paymentGateway->paystack_merchant_email;
            $this->paystackMode = $this->paymentGateway->paystack_mode;
            $this->testPaystackKey = $this->paymentGateway->test_paystack_key;
            $this->testPaystackSecret = $this->paymentGateway->test_paystack_secret;
            $this->testPaystackMerchantEmail = $this->paymentGateway->test_paystack_merchant_email;
            $this->isPaystackEnabled = $this->paymentGateway->paystack_status;

            $this->payfastMerchantId = $this->paymentGateway->payfast_merchant_id;
            $this->payfastMerchantKey = $this->paymentGateway->payfast_merchant_key;
            $this->payfastPassphrase = $this->paymentGateway->payfast_passphrase;
            $this->payfastMode = $this->paymentGateway->payfast_mode;
            $this->payfastStatus = (bool)$this->paymentGateway->payfast_status;
            $this->testPayfastMerchantId = $this->paymentGateway->test_payfast_merchant_id;
            $this->testPayfastMerchantKey = $this->paymentGateway->test_payfast_merchant_key;
            $this->testPayfastPassphrase = $this->paymentGateway->test_payfast_passphrase;
            $this->isPayfastEnabled = $this->paymentGateway->payfast_status;

            $this->enablePayViaCash = (bool)$this->paymentGateway->is_cash_payment_enabled;
            if (!$this->razorpayStatus && !$this->stripeStatus && !$this->flutterwaveStatus && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
                $this->enablePayViaCash = true;
            }
        }
        
        if ($this->activePaymentSetting === 'stripe') {
            $hash = society()->hash;
            $this->webhookUrl = route('stripe.webhook', ['hash' => $hash]);
        }
        if ($this->activePaymentSetting === 'razorpay') {
            $hash = society()->hash;
            $this->webhookUrl = route('razorpay.webhook', ['hash' => $hash]);
        }
        if ($this->activePaymentSetting === 'flutterwave') {
            $hash = society()->hash;
            $this->webhookUrl = route('flutterwave.webhook', ['hash' => $hash]);
        }
        if ($this->activePaymentSetting === 'paypal') {
            $hash = society()->hash;
            $this->webhookUrl = route('paypal.webhook', ['hash' => $hash]);
        }
        if ($this->activePaymentSetting === 'paystack') {
            $hash = society()->hash;
            $this->webhookUrl = route('paystack.webhook', ['hash' => $hash]);
        }
    }

    public function submitFormRazorpay()
    {
        $this->validate([
            'razorpaySecret' => 'required_if:razorpayStatus,true',
            'razorpayKey' => 'required_if:razorpayStatus,true',
        ]);

        if ($this->saveRazorpaySettings() === 0) {
            $this->updatePaymentStatus();
            $this->alertSuccess();
        }

        if (!$this->razorpayStatus && !$this->stripeStatus  && !$this->flutterwaveStatus  && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
            $this->enablePayViaCash = true;
        }
    }

    public function submitFormStripe()
    {
        $this->validate([
            'stripeSecret' => 'required_if:stripeStatus,true',
            'stripeKey' => 'required_if:stripeStatus,true',
        ]);

        if ($this->saveStripeSettings() === 0) {
            $this->updatePaymentStatus();
            $this->alertSuccess();
        }

        if (!$this->razorpayStatus && !$this->stripeStatus  && !$this->flutterwaveStatus && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
            $this->enablePayViaCash = true;
        }
    }

    public function submitFormOffline()
    {
        $rules = [
            'enablePayViaCash' => 'required|boolean'
        ];

        $this->validate($rules);

        $updateData = [
            'is_cash_payment_enabled' => $this->enablePayViaCash,

        ];

        $this->paymentGateway->update($updateData);

        $this->updatePaymentStatus();
        $this->alertSuccess();
        if (!$this->razorpayStatus && !$this->stripeStatus  && !$this->flutterwaveStatus  && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
            $this->enablePayViaCash = true;
        }
    }

    private function saveRazorpaySettings()
    {
        if (!$this->razorpayStatus) {
            $this->paymentGateway->update([
                'razorpay_status' => $this->razorpayStatus,
            ]);
            return 0;
        }

        try {
            $response = Http::withBasicAuth($this->razorpayKey, $this->razorpaySecret)
                ->get('https://api.razorpay.com/v1/contacts');

            if ($response->successful()) {
                $this->paymentGateway->update([
                    'razorpay_key' => $this->razorpayKey,
                    'razorpay_secret' => $this->razorpaySecret,
                    'razorpay_webhook_key' => $this->razorpayWebhookKey,
                    'razorpay_status' => $this->razorpayStatus,
                ]);
                return 0;
            }

            $this->addError('razorpayKey', 'Invalid Razorpay key or secret.');
        } catch (\Exception $e) {
            $this->addError('razorpayKey', 'Error: ' . $e->getMessage());
        }

        return 1;
    }

    public function submitFormFlutterWave()
    {

        $this->validate([
            'flutterwaveStatus' => 'required|boolean',
            'flutterwaveMode' => 'required_if:flutterwaveStatus,true',
            'liveFlutterwaveKey' => 'required_if:flutterwaveMode,live',
            'liveFlutterwaveSecret' => 'required_if:flutterwaveMode,live',
            'liveFlutterwaveHash' => 'required_if:flutterwaveMode,live',
            'testFlutterwaveKey' => 'required_if:flutterwaveMode,sandbox',
            'testFlutterwaveSecret' => 'required_if:flutterwaveMode,sandbox',
            'testFlutterwaveHash' => 'required_if:flutterwaveMode,sandbox',

        ]);

        if ($this->saveFlutterwaveSettings() === 0) {
            $this->updatePaymentStatus();
            $this->alertSuccess();
        }

        if (!$this->razorpayStatus && !$this->stripeStatus  && !$this->flutterwaveStatus && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
            $this->enablePayViaCash = true;
        }
    }

    private function saveFlutterwaveSettings()
    {
        if (!$this->flutterwaveStatus) {
            $this->paymentGateway->update([
                'flutterwave_status' => $this->flutterwaveStatus,

            ]);

            return 0;
        }

        try {
            $apiKey = $this->flutterwaveMode === 'live' ? $this->liveFlutterwaveKey : $this->testFlutterwaveKey;
            $apiSecret = $this->flutterwaveMode === 'live' ? $this->liveFlutterwaveSecret : $this->testFlutterwaveSecret;

            $response = Http::withToken($apiSecret)
            ->get('https://api.flutterwave.com/v3/transactions');

            if ($response->successful()) {
                $this->paymentGateway->update([
                    'flutterwave_mode' => $this->flutterwaveMode,
                    'flutterwave_status' => $this->flutterwaveStatus,
                    'live_flutterwave_key' => $this->liveFlutterwaveKey,
                    'live_flutterwave_secret' => $this->liveFlutterwaveSecret,
                    'live_flutterwave_hash' => $this->liveFlutterwaveHash,
                    'test_flutterwave_key' => $this->testFlutterwaveKey,
                    'test_flutterwave_secret' => $this->testFlutterwaveSecret,
                    'test_flutterwave_hash' => $this->testFlutterwaveHash,
                    'flutterwave_webhook_secret_hash' => $this->flutterwaveWebhookSecretHash,
                ]);

                return 0;
            }
            $this->addError('flutterwaveKey', 'Invalid Flutterwave key or secret.');
        } catch (\Exception $e) {
            $this->addError('flutterwaveKey', 'Error: ' . $e->getMessage());
        }

        return 1;
    }

    public function submitFormPaypal()
    {
        $this->validate([
            'paypalStatus' => 'required|boolean',
            'paypalMode' => 'required_if:paypalStatus,true', // live or sandbox
            'livePaypalClientId' => 'required_if:paypalMode,live',
            'livePaypalSecret' => 'required_if:paypalMode,live',
            'sandboxPaypalClientId' => 'required_if:paypalMode,sandbox',
            'sandboxPaypalSecret' => 'required_if:paypalMode,sandbox',
        ]);

        if ($this->savePaypalSettings() === 0) {
            $this->updatePaymentStatus();
            $this->alertSuccess();
        }

        if (!$this->razorpayStatus && !$this->stripeStatus && !$this->flutterwaveStatus && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
            $this->enablePayViaCash = true;
        }
    }

    public function savePaypalSettings()
    {


        if (!$this->paypalStatus) {
            $this->paymentGateway->update([
                'paypal_status' => $this->paypalStatus,
            ]);
            return 0;
        }

        try {
            $apiKey = $this->paypalMode === 'live' ? $this->livePaypalClientId : $this->sandboxPaypalClientId;
            $apiSecret = $this->paypalMode === 'live' ? $this->livePaypalSecret : $this->sandboxPaypalSecret;

            $url = $this->paypalMode === 'live'
            ? 'https://api.paypal.com/v1/oauth2/token'
            : 'https://api.sandbox.paypal.com/v1/oauth2/token';

        $response = Http::withBasicAuth($apiKey, $apiSecret)
            ->asForm()
            ->post($url, [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $this->paymentGateway->update([
                    'paypal_mode' => $this->paypalMode,
                    'paypal_status' => $this->paypalStatus,
                    'sandbox_paypal_client_id' => $this->sandboxPaypalClientId,
                    'sandbox_paypal_secret' => $this->sandboxPaypalSecret,
                    'paypal_client_id' => $this->livePaypalClientId,
                    'paypal_secret' => $this->livePaypalSecret,
                ]);
                return 0;
            }

            $this->addError('paypalKey', 'Invalid Paypal key or secret.');
        } catch (\Exception $e) {
            $this->addError('paypalKey', 'Error: ' . $e->getMessage());
        }

        return 1;
    }



    private function saveStripeSettings()
    {

        if (!$this->stripeStatus) {
            $this->paymentGateway->update([
                'stripe_status' => $this->stripeStatus,
            ]);
            return 0;
        }

        try {
            $response = Http::withToken($this->stripeSecret)
                ->get('https://api.stripe.com/v1/customers');

            if ($response->successful()) {
                $this->paymentGateway->update([
                    'stripe_key' => $this->stripeKey,
                    'stripe_secret' => $this->stripeSecret,
                    'stripe_webhook_key' => $this->stripeWebhookKey,
                    'stripe_status' => $this->stripeStatus,
                ]);
                return 0;
            }

            $this->addError('stripeKey', 'Invalid Stripe key or secret.');
        } catch (\Exception $e) {
            $this->addError('stripeKey', 'Error: ' . $e->getMessage());
        }

        return 1;
    }

    public function submitFormPaystack()
    {

        $this->validate([
            'testPaystackKey' => 'nullable|required_if:paystackMode,sandbox',
            'testPaystackSecret' => 'nullable|required_if:paystackMode,sandbox',
            'testPaystackMerchantEmail' => 'nullable|required_if:paystackMode,sandbox|email',

            'paystackKey' => 'nullable|required_if:paystackMode,live',
            'paystackSecret' => 'nullable|required_if:paystackMode,live',
            'paystackMerchantEmail' => 'nullable|required_if:paystackMode,live|email',
            ]);

        if ($this->savePaystackSettings() === 0) {

            $this->updatePaymentStatus();
            $this->alertSuccess();
        }

        if (!$this->razorpayStatus && !$this->stripeStatus && !$this->flutterwaveStatus && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
            $this->enablePayViaCash = true;
        }
    }

    private function savePaystackSettings()
    {
        if (!$this->paystackStatus) {
            $this->paymentGateway->update([
                'paystack_status' => $this->paystackStatus,
            ]);
            return 0;
        }

        try {
            $apiSecret = $this->paystackMode === 'live' ? $this->paystackSecret : $this->testPaystackSecret;

            $response = Http::withToken($apiSecret)
                    ->get('https://api.paystack.co/transaction');
            if ($response->successful()) {
                $this->paymentGateway->update([
                    'paystack_key' => $this->paystackKey,
                    'paystack_secret' => $this->paystackSecret,
                    'paystack_merchant_email' => $this->paystackMerchantEmail,
                    'paystack_mode' => $this->paystackMode,
                    'test_paystack_key' => $this->testPaystackKey,
                    'test_paystack_secret' => $this->testPaystackSecret,
                    'test_paystack_merchant_email' => $this->testPaystackMerchantEmail,
                    'paystack_payment_url' => $this->paymentGateway->paystack_payment_url,
                    'paystack_status' => $this->paystackStatus,
                ]);
                return 0;
            }

            $this->addError('paystackKey', 'Invalid Paystack key or secret.');
        } catch (\Exception $e) {
            $this->addError('paystackKey', 'Error: ' . $e->getMessage());
        }

        return 1;
    }

    public function submitFormPayfast()
    {
        $this->validate([
            'testPayfastMerchantId' => 'nullable|required_if:payfastMode,sandbox',
            'testPayfastMerchantKey' => 'nullable|required_if:payfastMode,sandbox',
            'payfastMerchantId' => 'nullable|required_if:payfastMode,live',
            'payfastMerchantKey' => 'nullable|required_if:payfastMode,live',
            'payfastPassphrase' => 'nullable|required_if:payfastMode,live',
            ]);

        if ($this->savePayfastSettings() === 0) {

            $this->updatePaymentStatus();
            $this->alertSuccess();
        }

        if (!$this->razorpayStatus && !$this->stripeStatus && !$this->flutterwaveStatus && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
            $this->enablePayViaCash = true;
        }
    }

    private function savePayfastSettings()
    {

        if (!$this->payfastStatus) {
            $this->paymentGateway->update([
                'payfast_status' => $this->payfastStatus,
            ]);
            return 0;
        }

        try {
            $this->paymentGateway->update([
                'payfast_merchant_id' => $this->payfastMerchantId,
                'payfast_merchant_key' => $this->payfastMerchantKey,
                'payfast_passphrase' => $this->payfastPassphrase,
                'payfast_mode' => $this->payfastMode,
                'test_payfast_merchant_id' => $this->testPayfastMerchantId,
                'test_payfast_merchant_key' => $this->testPayfastMerchantKey,
                'test_payfast_passphrase' => $this->testPayfastPassphrase,
                'payfast_status' => $this->payfastStatus,
            ]);

            return 0;
        } catch (\Exception $e) {
            $this->addError('payfastKey', 'Error saving Payfast settings: ' . $e->getMessage());
            return 1;
        }
    }


    public function updatePaymentStatus()
    {
        if (!$this->razorpayStatus && !$this->stripeStatus && !$this->flutterwaveStatus && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
            $this->enablePayViaCash = true;
        }

        if ($this->razorpayStatus || $this->stripeStatus || $this->flutterwaveStatus || $this->paypalStatus || $this->paystackStatus || $this->payfastStatus) {
            $this->enablePayViaCash = $this->enablePayViaCash;
        } else {
            $this->enablePayViaCash = true;
        }

        // Save updates in the database
        $this->paymentGateway->update([
            'razorpay_status' => $this->razorpayStatus,
            'stripe_status' => $this->stripeStatus,
            'flutterwave_status' => $this->flutterwaveStatus,
            'paypal_status' => $this->paypalStatus,
            'paystack_status' => $this->paystackStatus,
            'payfast_status' => $this->payfastStatus,
            'is_cash_payment_enabled' => $this->enablePayViaCash,
        ]);

        $this->setCredentials();
        $this->dispatch('settingsUpdated');
        session()->forget('paymentGateway');
    }

    public function alertSuccess()
    {
        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close'),
        ]);
    }

    public function render()
    {
        return view('livewire.settings.payment-settings');
    }
}
