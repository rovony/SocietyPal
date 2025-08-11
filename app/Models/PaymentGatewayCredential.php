<?php

namespace App\Models;

use App\Traits\HasSociety;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewayCredential extends Model
{

    use HasFactory, HasSociety;
    protected $table = 'payment_gateway_credentials';
    protected $guarded = ['id'];

    protected $attributes = [
        'is_cash_payment_enabled' => true,
    ];

    protected $casts = [
        'stripe_key' => 'encrypted',
        'razorpay_key' => 'encrypted',
        'stripe_secret' => 'encrypted',
        'razorpay_secret' => 'encrypted',
    ];

    public function getFlutterwaveKeyAttribute()
    {
        return ($this->flutterwave_mode == 'sandbox' ? $this->test_flutterwave_key : $this->live_flutterwave_key);
    }
    public function getFlutterwaveSecretAttribute()
    {
        return ($this->flutterwave_mode == 'sandbox' ? $this->test_flutterwave_secret : $this->live_flutterwave_secret);
    }
    public function getFlutterwaveHashAttribute()
    {
        return ($this->flutterwave_mode == 'sandbox' ? $this->test_flutterwave_hash : $this->live_flutterwave_hash);
    }

    public function getPaypalClientIdDataAttribute()
    {
        return ($this->paypal_mode == 'sandbox' ? $this->sandbox_paypal_client_id : $this->paypal_client_id);
    }
    public function getPaypalSecretDataAttribute()
    {
        return ($this->paypal_mode == 'sandbox' ? $this->sandbox_paypal_secret : $this->paypal_secret);
    }

    public function getPaystackKeyDataAttribute()
    {
        return ($this->paystack_mode == 'sandbox' ? $this->test_paystack_key : $this->paystack_key);
    }
    public function getPaystackSecretDataAttribute()
    {
        return ($this->paystack_mode == 'sandbox' ? $this->test_paystack_secret : $this->paystack_secret);
    }
    public function getPaystackMerchantEmailDataAttribute()
    {
        return ($this->paystack_mode == 'sandbox' ? $this->test_paystack_merchant_email : $this->paystack_merchant_email);
    }

    public function getPayfastMerchantIdDataAttribute()
    {
        return ($this->payfast_mode == 'sandbox' ? $this->test_payfast_merchant_id : $this->payfast_merchant_id);
    }
    public function getPayfastMerchantKeyDataAttribute()
    {
        return ($this->payfast_mode == 'sandbox' ? $this->test_payfast_merchant_key : $this->payfast_merchant_key);
    }

    public function getPayfastPassphraseDataAttribute()
    {
        return ($this->payfast_mode == 'sandbox' ? $this->test_payfast_passphrase : $this->payfast_passphrase);
    }

}
