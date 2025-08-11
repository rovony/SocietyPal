<?php

namespace App\Http\Controllers;

use Error;
use App\Models\Payment;
use App\Models\StripePayment;
use App\Models\MaintenanceApartment;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class AdminStripeController extends Controller
{
  use WithFileUploads, LivewireAlert;

    public function maintenancePayment()
    {

        $milestonePayment = StripePayment::findOrFail(request()->order_payment);
        $paymentGateway = society()->paymentGateways;

        $stripe = new \Stripe\StripeClient($paymentGateway->stripe_secret);

        $checkoutSession = $stripe->checkout->sessions->create([
          'line_items' => [[
            'price_data' => [
              'currency' => society()->currency->currency_code,
              'product_data' => [
                'name' => 'Order #' .$milestonePayment->maintenance_apartment,
              ],
              'unit_amount' => floatval($milestonePayment->amount) * 100,
            ],
            'quantity' => 1,
          ]],
          'mode' => 'payment',
          'success_url' => route('stripe.success').'?session_id={CHECKOUT_SESSION_ID}',
          'client_reference_id' => $milestonePayment->id
        ]);

        $milestonePayment->stripe_session_id = $checkoutSession->id;
        $milestonePayment->save();

        header('HTTP/1.1 303 See Other');
        return redirect($checkoutSession->url);
    }

    public function success()
    {
        $payment = StripePayment::where('stripe_session_id', request()->session_id)->firstOrFail();
        $paymentGateway = society()->paymentGateways;

        $stripe = new \Stripe\StripeClient($paymentGateway->stripe_secret);

        try {
            $session = $stripe->checkout->sessions->retrieve(request()->session_id);

            $payment->stripe_payment_intent = $session->payment_intent;
            $payment->payment_status = 'completed';
            $payment->payment_date = now()->toDateTimeString();
            $payment->save();

            Payment::create([
                'maintenance_apartment_id' => $payment->maintenance_apartment_id,
                'payment_method' => 'stripe',
                'amount' => $payment->amount,
                'transaction_id' => $session->payment_intent
            ]);

            $order = MaintenanceApartment::find($payment->maintenance_apartment_id);
            $order->cost = $payment->amount;
            $order->payment_date = $payment->payment_date;
            $order->paid_status = 'paid';
            $order->save();



            return redirect()->route('maintenance.index');
        } catch (Error $e) {
            http_response_code(500);
            logger(json_encode(['error' => $e->getMessage()]));
        }

    }

}
