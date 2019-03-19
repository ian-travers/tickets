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
 * @property FakePaymentGateway $paymentGateway
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

    private function orderTickets($concert, $params)
    {
//        $savedRequest = $this->app['request'];

        return $this->json('post', "/concerts/{$concert->id}/orders", $params);

//        $this->app['request'] = $savedRequest;
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
        $response->assertJsonFragment([
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'amount' => 9750,
        ]);
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
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
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
        $this->assertEquals(10, $concert->ticketsRemaining());
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
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    public function test_cannot_purchase_tickets_another_customer_is_already_trying_to_purchase()
    {
                            // A RACE CONDITION
        // Find tickets for person A
                                        // Find tickets for person B
                                        // Attempt to charge person B
                                        // Create an order for person B
        // Attempt to charge person A
        // Create an order for person A

        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('published')->create([
            'ticket_price' => 1200,
        ])->addTickets(3);

        $this->paymentGateway->beforeFirstCharge(function ($paymentGateway) use($concert) {

            $savedRequest = $this->app['request'];

            $response = $this->orderTickets($concert, [
                'email' => 'person_B@example.com',
                'ticket_quantity' => 1,
                'payment_token' => $this->paymentGateway->getValidTestToken(),
            ]);

            $this->app['request'] = $savedRequest;

            $response->assertStatus(422);
            $this->assertFalse($concert->hasOrderFor('person_B@example.com'));
            $this->assertEquals(0, $this->paymentGateway->totalCharges());
        });

        $response = $this->orderTickets($concert, [
            'email' => 'person_A@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);
//        dd($concert->orders()->first()->toArray());
        $this->assertEquals(3600, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('person_A@example.com'));
        $this->assertEquals(3, $concert->ordersFor('person_A@example.com')->first()->ticketQuantity());
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

