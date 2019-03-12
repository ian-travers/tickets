<?php

namespace Tests\Feature;

use App\Billing\PaymentGatewayInterface;
use App\Concert;
use App\Order;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;

/**
 * Class PurchaseTicketsTest
 *
 * @property PaymentGatewayInterface $paymentGateway
 *
 * @package Tests\Feature
 */
class PurchaseTicketsTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGatewayInterface::class, $this->paymentGateway);
    }

    public function test_customer_can_purchase_concert_tickets()
    {
        // Arrange. Create a concert
        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        // Act. Purchase concert tickets
        $response = $this->json('post', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Asserts
        $response->assertStatus(201);

        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        // Make sure that an order exists for this customer
        /* @var Order $order */
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    public function test_email_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();

        $response = $this->json('post', "/concerts/{$concert->id}/orders", [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->decodeResponseJson());
        $this->assertArrayHasKey('email', $response->decodeResponseJson('errors'));
    }
}

