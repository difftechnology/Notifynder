<?php

namespace Difftechnology\Notifynder\Senders;

use Difftechnology\Notifynder\Contracts\DefaultSender;
use Difftechnology\Notifynder\Contracts\StoreNotification;
use Difftechnology\Notifynder\Exceptions\CategoryNotFoundException;

/**
 * Class SendSingle.
 *
 * Send a single notification
 */
class SendOne implements DefaultSender
{
    /**
     * @var array
     */
    protected $infoNotification = [];

    /**
     * @param   $infoNotification
     */
    public function __construct($infoNotification)
    {
        $this->infoNotification = $infoNotification;
    }

    /**
     * Send Single notification.
     *
     * @param  StoreNotification $sender
     * @return mixed
     */
    public function send(StoreNotification $sender)
    {
        $this->hasCategory();

        return $sender->storeSingle($this->infoNotification);
    }

    /**
     * Check if the category of the notification has been
     * specified in the array of information.
     *
     * @return bool
     * @throws \Difftechnology\Notifynder\Exceptions\CategoryNotFoundException
     */
    protected function hasCategory()
    {
        if (! array_key_exists('category_id', $this->infoNotification)) {
            $error = 'Category not found please provide one,
                     you can not store a notification without category id';

            throw new CategoryNotFoundException($error);
        }

        return true;
    }
}
