<?php

namespace App\Livewire\Forms;

use Carbon\Carbon;
use App\Helper\Files;
use Razorpay\Api\Api;
use App\Models\Payment;
use App\Models\Society;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\PaypalPayment;
use App\Models\StripePayment;
use App\Models\RazorpayPayment;
use App\Models\AdminPaystackPayment;
use App\Models\MaintenanceApartment;
use Illuminate\Support\Facades\Http;
use App\Models\AdminFlutterwavePayment;
use App\Models\AdminPayfastPayment;
use App\Models\PaymentGatewayCredential;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Facades\Log;

class MaintenancePay extends Component
{
    use WithFileUploads, LivewireAlert;

    public $file;
    public $fileUrl;
    public $paymentProof;
    public $apartment_maintenance;
    public $paid_status;
    public $paymentDate;
    public $billDate;
    public $showPayUtilityBillModal = false;
    public $id;
    public $society;
    public $paymentGateway;
    public $paymentOrder;
    public $razorpayStatus;
    public $stripeStatus;
    public $showPaymentDetail = false;
    public $amount;
    public $paymentMethod = 'offline';

    public function mount()
    {
        $this->paid_status = $this->apartment_maintenance->paid_status;
        $this->amount = $this->apartment_maintenance->cost;
        $this->paymentProof = $this->apartment_maintenance->payment_proof;
        $this->paymentDate = now()->format('Y-m-d');
        $this->paymentGateway = PaymentGatewayCredential::withoutGlobalScopes()->where('society_id', society()->id)->first();
        $this->paymentOrder = null;
        $this->society = $this->getSociety();
    }

    private function getSociety()
    {
        return Society::where('id', society()->id)->first();
    }

    public function submitForm()
    {
        if ($this->paymentProof) {
            $filename = Files::uploadLocalOrS3($this->paymentProof, MaintenanceApartment::FILE_PATH . '/');
            $this->paymentProof = $filename;
            $this->apartment_maintenance->payment_proof = $this->paymentProof;
        }

        $this->apartment_maintenance->paid_status = "payment_requested";
        $this->apartment_maintenance->save();


        $this->alert('success', __('messages.maintenancePaid'));

        $this->dispatch('hidePay');
    }


    #[On('resetFileData')]
    public function resetFileData()
    {

        $this->paymentProof = '';
        $this->resetValidation();
    }

    public function initiateStripePayment($id)
    {


        $payment = StripePayment::create([
            'maintenance_apartment_id' => $id,
            'amount' => $this->amount
        ]);

        $this->dispatch('stripePaymentInitiated', payment: $payment);
    }

    public function initiatePayment($id)
    {
        $payment = RazorpayPayment::create([
            'maintenance_apartment_id' => $id,
            'amount' => $this->amount
        ]);

        $orderData = [
            'amount' => ($this->amount * 100),
            'currency' => $this->society->currency->currency_code
        ];

        $apiKey = $this->society->paymentGateways->razorpay_key;
        $secretKey = $this->society->paymentGateways->razorpay_secret;

        $api  = new Api($apiKey, $secretKey);
        $razorpayOrder = $api->order->create($orderData);
        $payment->razorpay_order_id = $razorpayOrder->id;
        $payment->save();

        $this->dispatch('paymentInitiated', payment: $payment);
    }

    #[On('razorpayPaymentCompleted')]
    public function razorpayPaymentCompleted($razorpayPaymentID, $razorpayOrderID, $razorpaySignature)
    {
        $payment = RazorpayPayment::where('razorpay_order_id', $razorpayOrderID)
            ->where('payment_status', 'pending')
            ->first();

        if ($payment) {
            $payment->razorpay_payment_id = $razorpayPaymentID;
            $payment->payment_status = 'completed';
            $payment->payment_date = now()->toDateTimeString();
            $payment->razorpay_signature = $razorpaySignature;
            $payment->save();

            $order = MaintenanceApartment::find($payment->maintenance_apartment_id);
            $order->cost = $payment->amount;
            $order->payment_date = $payment->payment_date;
            $order->paid_status = 'paid';
            $order->save();

            Payment::create([
                'maintenance_apartment_id' => $payment->maintenance_apartment_id,
                'payment_method' => 'razorpay',
                'amount' => $payment->amount,
                'transaction_id' => $razorpayPaymentID
            ]);

            $this->alert('success', __('messages.maintenancePaid'), [
                'toast' => false,
                'position' => 'center',
                'showCancelButton' => true,
                'cancelButtonText' => __('app.close')
            ]);


            return redirect()->route('maintenance.index');
        }

    }
    public function initiatePaypalPayment($id)
    {
        $amount = $this->amount;
        $currency = strtoupper($this->society->currency->currency_code);

        $unsupportedCurrencies = ['INR'];
        if (in_array($currency, $unsupportedCurrencies)) {
            session()->flash('flash.banner', 'Currency not supported by PayPal.');
            session()->flash('flash.bannerStyle', 'warning');
            return redirect()->route('maintenance.index');
        }

        $clientId = $this->paymentGateway->paypal_client_id_data;
        $secret = $this->paymentGateway->paypal_secret_data;

        $paypalPayment = new PaypalPayment();
        $paypalPayment->maintenance_apartment_id = $id;
        $paypalPayment->amount = $amount;
        $paypalPayment->payment_status = 'pending';
        $paypalPayment->save();

        $returnUrl = route('paypal.success');
        $cancelUrl = route('paypal.cancel');

        $paypalData = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => $currency,
                    "value" => number_format($amount, 2, '.', '')
                ],
                "reference_id" => (string)$paypalPayment->id
            ]],
            "application_context" => [
                "return_url" => $returnUrl,
                "cancel_url" => $cancelUrl
            ]
        ];

        $auth = base64_encode("$clientId:$secret");

        $response = Http::withHeaders([
            'Authorization' => "Basic $auth",
            'Content-Type' => 'application/json'
        ])->post('https://api-m.sandbox.paypal.com/v2/checkout/orders', $paypalData);

        if ($response->successful()) {
            $paypalResponse = $response->json();

            $paypalPayment->paypal_payment_id = $paypalResponse['id'];
            $paypalPayment->payment_status = 'pending';
            $paypalPayment->save();

            $approvalLink = collect($paypalResponse['links'])->firstWhere('rel', 'approve')['href'];
            return redirect($approvalLink);
        }
            $paypalPayment->payment_status = 'failed';
            $paypalPayment->save();

            return redirect()->route('paypal.cancel');

    }







    public function initiateFlutterwavePayment($id)
    {

        try {
            $paymentGateway = $this->society->paymentGateways;
            $apiKey = $paymentGateway->flutterwave_key;
            $apiSecret = $paymentGateway->flutterwave_secret;



            $user = auth()->user();
            $amount = $this->amount;

            $tx_ref = "txn_" . time();

            $data = [
                "tx_ref" => $tx_ref,
                "amount" => $amount,
                "currency" => $this->society->currency->currency_code,
                "redirect_url" => route('flutterwave.success'),
                "customer" => [
                    "email" => $user->email,
                    "name" => $user->name,
                    "phone_number" => $user->phone,
                ],


                "payment_options" => "card",
            ];

            $response = Http::withHeaders([
                "Authorization" => "Bearer $apiSecret",
                "Content-Type" => "application/json"
            ])->post("https://api.flutterwave.com/v3/payments", $data);

            $responseData = $response->json();

            if (isset($responseData['status']) && $responseData['status'] === 'success') {
                AdminFlutterwavePayment::create([
                    'maintenance_apartment_id' => $id,
                    'flutterwave_payment_id' => $tx_ref,
                    'amount' => $amount,
                    'payment_status' => 'pending',
                ]);

                return redirect($responseData['data']['link']);
            } else {
                return redirect()->route('flutterwave.failed')->withErrors(['error' => 'Payment initiation failed', 'message' => $responseData]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function initiatePaystackPayment($id)
    {
        try {
            $paymentGateway = $this->society->paymentGateways;
            $secretKey = $paymentGateway->paystack_secret_data;

            $user = auth()->user();
            $amount = $this->amount * 100; // Paystack expects amount in kobo

            $reference = "psk_" . time();

            $data = [
                "reference" => $reference,
                "amount" => $amount,
                "email" => $user->email,
                "currency" => 'ZAR',
                "callback_url" => route('paystack.success'),
            "metadata" => [
            "cancel_action" => route('paystack.failed', ['reference' => $reference])
            ]

            ];

            $response = Http::withHeaders([
                "Authorization" => "Bearer $secretKey",
                "Content-Type" => "application/json"
            ])->post("https://api.paystack.co/transaction/initialize", $data);

            $responseData = $response->json();


            if (isset($responseData['status']) && $responseData['status'] === true) {
                AdminPaystackPayment::create([
                    'maintenance_apartment_id' => $id,
                    'paystack_payment_id' => $reference,
                    'amount' => $this->amount,
                    'payment_status' => 'pending',
                ]);

                return redirect($responseData['data']['authorization_url']);
            } else {

                session()->flash('error', 'Payment initiation failed.');
                return redirect()->route('paystack.failed');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function generateSignature($data, $passPhrase) {
        $pfOutput = '';
        foreach( $data as $key => $val ) {
            if($val !== '') {
                $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
            }
        }
        $getString = substr( $pfOutput, 0, -1 );
        if( $passPhrase !== null ) {
            $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
        }

        return md5( $getString );


    }

    public function initiatePayfastPayment($id)
    {
        $paymentGateway = $this->society->paymentGateways;
        $isSandbox = $paymentGateway->payfast_mode === 'sandbox';
        $merchantId = $isSandbox ? $paymentGateway->test_payfast_merchant_id : $paymentGateway->payfast_merchant_id;
        $merchantKey = $isSandbox ? $paymentGateway->test_payfast_merchant_key : $paymentGateway->payfast_merchant_key;
        $passphrase = $isSandbox ? $paymentGateway->test_payfast_passphrase : $paymentGateway->payfast_passphrase;
        $amount = number_format($this->amount, 2, '.', '');
        $itemName = "Maintenance Payment #$id";
        $reference = 'pf_' . time();
        $data = [
            'merchant_id' => $merchantId,
            'merchant_key' => $merchantKey,
            'return_url' => route('payfast.success', ['reference' => $reference]),
            'cancel_url' => route('payfast.failed', ['reference' => $reference]),
            'notify_url' => route('payfast.notify', ['company' => $this->society->hash, 'reference' => $reference]),

            'name_first' => auth()->user()->name,
            'email_address' => auth()->user()->email,
            'm_payment_id' => $id, // Your internal ID
            'amount' => $amount,
            'item_name' => $itemName,
        ];


        $signature = $this->generateSignature($data, $passphrase);
        $data['signature'] = $signature;

        AdminPayfastPayment::create([
            'maintenance_apartment_id' => $id,
            'payfast_payment_id' => $reference,
            'amount' => $amount,
            'payment_status' => 'pending',
        ]);

        $payfastBaseUrl = $isSandbox ? 'https://sandbox.payfast.co.za/eng/process' : 'https://api.payfast.co.za/eng/process';
        $redirectUrl = $payfastBaseUrl . '?' . http_build_query($data);
        return redirect($redirectUrl);
    }


    public function removeProfilePhoto()
    {
        $this->paymentProof = null;
        $this->dispatch('photo-removed');
    }

    public function togglePaymenntDetail()
    {
        $this->showPaymentDetail = !$this->showPaymentDetail;
    }

    public function render()
    {
        return view('livewire.forms.maintenance-pay');
    }
}
