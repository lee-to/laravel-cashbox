<?php

namespace App\CashBox\PaymentNotifications;

use App\CashBox\PaymentNotificationInterface;

use TelegramBot\Api\BotApi;

class Telegram extends PaymentNotification implements PaymentNotificationInterface
{
    public function __construct()
    {
        $this->setClient(config("cashbox.notify.telegram.token") ? new BotApi(config("cashbox.notify.telegram.token")) : null);
    }

    public function send(string $message)
    {
        if(!config("cashbox.notify.telegram.chat_id")) {
            return false;
        }

        return $this->getClient() ? $this->getClient()->sendMessage(config("cashbox.notify.telegram.chat_id"), $message) : false;
    }
}