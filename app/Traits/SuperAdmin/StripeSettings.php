<?php
/**
 * Created by PhpStorm.
 * User: DEXTER
 * Date: 24/05/17
 * Time: 11:29 PM
 */

namespace App\Traits\SuperAdmin;

use App\Models\SuperadminPaymentGateway;
use Illuminate\Support\Facades\Config;
use Stripe\Stripe;

trait StripeSettings
{
    public function setStripConfigs()
    {
        $settings = SuperadminPaymentGateway::first();

        if ($settings->stripe_type == 'test') {
            $stripeKey = $settings->test_stripe_key;
            $stripeSecret = $settings->test_stripe_secret;
            $stripeWebhookSecret = $settings->stripe_test_webhook_key;
        }
        else {
            $stripeKey = $settings->live_stripe_key;
            $stripeSecret = $settings->live_stripe_secret;
            $stripeWebhookSecret = $settings->stripe_live_webhook_key;
        }

        $key = ($stripeKey) ?: env('STRIPE_KEY');
        $apiSecret = ($stripeSecret) ?: env('STRIPE_SECRET');
        $webhookKey = ($stripeWebhookSecret) ?: env('STRIPE_WEBHOOK_SECRET');

        Config::set('cashier.key', $key);
        Config::set('cashier.secret', $apiSecret);
        Config::set('cashier.webhook.secret', $webhookKey);
    }
}



