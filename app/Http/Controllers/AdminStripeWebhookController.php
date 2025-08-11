<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Society;
use Illuminate\Http\Request;
use App\Models\StripePayment;
use Illuminate\Routing\Controller;
use Stripe\Exception\SignatureVerificationException;

class AdminStripeWebhookController extends Controller
{
    /**
     * @param $societyHash
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWebhook($societyHash = null)
    {
        if (!$societyHash) {
            return response()->json([
                'message' => 'The route has been moved to another route. Please check the Stripe settings again.'
            ]);
        }

        $society = Society::where('hash', $societyHash)->first();
        if (!$society) {
            return response()->json([
                'message' => 'The webhook URL provided is incorrect.'
            ]);
        }

        return response()->json([
            'message' => 'This URL should not be opened directly (GET Request). Only POST requests are accepted. Add this URL to your Stripe webhook.'
        ]);
    }

    /**
     * Verify and process Stripe Webhook
     */
    public function verifyStripeWebhook(Request $request, $societyHash)
    {
        $society = Society::where('hash', $societyHash)->first();
        if (!$society) {
            return response()->json([
                'message' => 'Please enter the correct webhook URL. You have entered an incorrect webhook URL.'
            ]);
        }

        // Get Stripe Credentials
        $stripeCredentials = $society->paymentGateways;
        $stripeSecret = $stripeCredentials->stripe_secret;
        $webhookSecret = $stripeCredentials->stripe_webhook_key;

        if (is_null($webhookSecret)) {
            return response()->json([
                'error' => true,
                'message' => 'Webhook secret is not configured.',
            ], 400);
        }

        Stripe::setApiKey($stripeSecret);

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {
            Webhook::constructEvent($payload, $sig_header, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['message' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payload = json_decode($request->getContent(), true);
        $eventId = $payload['id'];
        $intentId = $payload['data']['object']['id'];
        $amount = $payload['data']['object']['amount'] / 100; // Convert cents to full currency amount

        if ($payload['data']['object']['status'] != 'succeeded') {
            $this->paymentFailed($payload);
            return response()->json(['message' => 'Payment failed'], 400);
        }

        // **If payment is successful**
        if ($payload['type'] == 'payment_intent.succeeded') {
            $existingPayment = StripePayment::where('stripe_payment_intent', $intentId)->first();

            if ($existingPayment) {
                $existingPayment->payment_status = 'completed';
                $existingPayment->payment_date = now();
                $existingPayment->save();
            }
        }

        return response()->json(['message' => 'Webhook handled successfully'], 200);
    }

    /**
     * Handle failed payments
     */
    public function paymentFailed($payload)
    {
        $intentId = $payload['data']['object']['id'];

        $code = $payload['data']['object']['charges']['data'][0]['failure_code'] ?? 'unknown_error';
        $message = $payload['data']['object']['charges']['data'][0]['failure_message'] ?? 'Payment failed';

        $errorMessage = json_encode(['code' => $code, 'message' => $message]);

        $payment = StripePayment::where('stripe_payment_intent', $intentId)->first();

        if ($payment) {
            $payment->payment_status = 'failed';
            $payment->payment_error_response = $errorMessage;
            $payment->save();
        }
    }
}
