<?php

namespace spec\Difftechnology\Notifynder\Senders;

use Difftechnology\Notifynder\Contracts\StoreNotification;
use PhpSpec\ObjectBehavior;

class SendMultipleSpec extends ObjectBehavior
{
    public function let()
    {
        $notifications = [];
        $this->beConstructedWith($notifications);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Difftechnology\Notifynder\Senders\SendMultiple');
    }

    /** @test */
    public function it_send_multiple_notification(StoreNotification $storeNotification)
    {
        $multiple = [
        ];

        $storeNotification->storeMultiple($multiple)->shouldBeCalled()
                ->willReturn(1);

        $this->send($storeNotification)->shouldReturn(1);
    }
}
