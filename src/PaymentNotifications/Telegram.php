<?php

namespace Leeto\CashBox\PaymentNotifications;

use Leeto\CashBox\PaymentNotificationInterface;

use TelegramBot\Api\BotApi;

/**
 * Class Telegram
 * @package Leeto\CashBox\PaymentNotifications
 */
class Telegram extends PaymentNotification implements PaymentNotificationInterface
{
    /**
     * Telegram constructor.
     */
    public function __construct()
    {
        $this->setClient(config("cashbox.notify.telegram.token") ? new BotApi(config("cashbox.notify.telegram.token")) : null);
    }

    /**
     * @param string $message
     * @return bool
     */
    public function send(string $message): bool
    {
        if(!config("cashbox.notify.telegram.chat_id")) {
            return false;
        }

        return $this->getClient()
            ? $this->getClient()->sendMessage(config("cashbox.notify.telegram.chat_id"), $message)
            : false;
    }
}