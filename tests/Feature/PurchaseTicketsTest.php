<?php

namespace Tests\Feature;

use App\Billing\PaymentGatewayInterface;
use App\Concert;
use App\Order;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;

class PurchaseTicketsTest extends TestCase
{
    public function test_customer_can_purchase_concert_tickets()
    {
        $paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGatewayInterface::class, $paymentGateway);

        // Arrange. Create a concert
        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        // Act. Purchase concert tickets
        $response = $this->json('post', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),
        ]);

        // Asserts
        $response->assertStatus(201);

        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $paymentGateway->totalCharges());

        // Make sure that an order exists for this customer
        /* @var Order $order */
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }
}

