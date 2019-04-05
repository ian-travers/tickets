<?php

namespace Tests\Unit\Mail;

use App\Mail\OrderConfirmationEmail;
use App\Order;
use Tests\TestCase;

class OrderConfirmationEmailTest extends TestCase
{
    public function test_email_contains_a_link_to_order_comfirmation_page()
    {
        $order = factory(Order::class)->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
        ]);

        $email = new OrderConfirmationEmail($order);
        $rendered = $email->render();

        $this->assertContains(url('/orders/ORDERCONFIRMATION1234'), $rendered);
    }

    public function test_email_has_a_subject()
    {
        $order = factory(Order::class)->make();
        $email = new OrderConfirmationEmail($order);

        $this->assertEquals('Your TicketBeast Order', $email->build()->subject);
    }
}