<?php

namespace Tests\Feature;

use App\Billing\PaymentGatewayInterface;
use App\Concert;
use App\Order;
use Illuminate\Foundation\Testing\TestResponse;
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

    private function orderTickets($concert, $params): TestResponse
    {
        return $this->json('post', "/concerts/{$concert->id}/orders", $params);
    }

    private function assertValidationError(TestResponse $response, string $field): void
    {
        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->decodeResponseJson());
        $this->assertArrayHasKey($field, $response->decodeResponseJson('errors'));
    }

    public function test_customer_can_purchase_tickets_to_a_published_concert()
    {
        // Arrange. Create a concert

        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250])->addTickets(3);

        // Act. Purchase concert tickets
        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Asserts
        $response->assertStatus(201);
        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        // Make sure that an order exists for this customer
        $this->assertTrue($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->ordersFor('john@example.com')->first()->ticketQuantity());
    }

    public function test_cannot_purchase_tickets_to_an_unpublished_concert()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('unpublished')->create()->addTickets(10);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 1,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(404);
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    public function test_an_order_is_not_created_if_payment_fails()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250])->addTickets(10);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 1,
            'payment_token' => 'invalid-payment-token',
        ]);

        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
    }

    public function test_cannot_purchase_more_tickets_then_remain()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('published')->create()->addTickets(50);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(422);
        /* @var Order $order */
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    public function test_email_is_required_to_purchase_tickets()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('published')->create()->addTickets(10);

        $response = $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'email');
    }

    public function test_email_must_be_valid_to_purchase_tickets()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('published')->create()->addTickets(10);

        $response = $this->orderTickets($concert, [
            'email' => 'not-a-valid-email',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'email');
    }

    public function test_ticket_quantity_is_required_to_purchase_tickets()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('published')->create()->addTickets(10);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'ticket_quantity');
    }

    public function test_ticket_quantity_must_be_least_1_to_purchase_tickets()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('published')->create()->addTickets(10);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'ticket_quantity');
    }

    public function test_payment_token_is_required()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('published')->create()->addTickets(10);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 1,
        ]);

        $this->assertValidationError($response, 'payment_token');
    }
}

