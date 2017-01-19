<?php

use Difftechnology\Notifynder\Contracts\NotifynderSender;
use Difftechnology\Notifynder\Contracts\Sender;

/**
 * Class CustomSender.
 */
class CustomDefaultSender implements Sender
{
    /**
     * @var array
     */
    protected $notifications;

    /**
     * @var \Difftechnology\Notifynder\NotifynderManager
     */
    private $notifynder;

    /**
     * @param array                        $notifications
     * @param \Difftechnology\Notifynder\NotifynderManager $notifynder
     */
    public function __construct(array $notifications, \Difftechnology\Notifynder\NotifynderManager $notifynder)
    {
        $this->notifications = $notifications;
        $this->notifynder = $notifynder;
    }

    /**
     * Send notification.
     *
     * @param NotifynderSender $sender
     * @return mixed
     */
    public function send(NotifynderSender $sender)
    {
        //        dd($storeNotification);
        return $sender->send($this->notifications);
    }
}
