<?php

namespace App\Livewire\Settings;

use App\Models\Society;
use App\Models\SocietyPayment;
use App\Models\SuperadminPaymentGateway;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Razorpay\Api\Api;

class UpgradeLicense extends Component
{
    use LivewireAlert;

    public $credential;
    public $showUpgradeModal = false;

    public function mount()
    {
        $this->credential = SuperadminPaymentGateway::first();
    }

    #[On('showUpgradeLicense')]
    public function showUpgradeLicense()
    {
        $this->showUpgradeModal = true;
    }

    public function initiatePayment()
    {
        $amount = package()->price;

        $payment = SocietyPayment::create([
            'society_id' => society()->id,
            'package_id' => package()->id,
            'amount' => $amount
        ]);

        $orderData = [
            'amount' => ($amount * 100),
            'currency' => package()->currency->currency_code
        ];

        $apiKey = $this->credential->razorpay_key;
        $secretKey = $this->credential->razorpay_secret;

        $api = new Api($apiKey, $secretKey);
        $razorpayOrder = $api->order->create($orderData);
        $payment->razorpay_order_id = $razorpayOrder->id;
        $payment->save();

        $this->dispatch('paymentInitiated', payment: $payment);
    }

    #[On('razorpayPaymentCompleted')]
    public function razorpayPaymentCompleted($razorpayPaymentID, $razorpayOrderID, $razorpaySignature)
    {
        $payment = SocietyPayment::where('razorpay_order_id', $razorpayOrderID)
            ->where('status', 'pending')
            ->first();

        if ($payment) {
            $payment->razorpay_payment_id = $razorpayPaymentID;
            $payment->status = 'paid';
            $payment->payment_date_time = now()->toDateTimeString();
            $payment->razorpay_signature = $razorpaySignature;
            $payment->transaction_id = $razorpayPaymentID;
            $payment->save();

            Society::where('id', $payment->society_id)->update(['license_type' => 'paid']);

            session(['society' => $payment->society]);

            $this->alert('success', __('messages.licenseUpgraded'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            $this->js('window.location.reload()');
        }

    }

    public function initiateStripePayment()
    {
        $amount = package()->price;

        $payment = SocietyPayment::create([
            'society_id' => society()->id,
            'package_id' => package()->id,
            'amount' => $amount
        ]);

        $this->dispatch('stripeLicensePaymentInitiated', payment: $payment);
    }
    public function render()
    {
        return view('livewire.settings.upgrade-license');
    }
}
