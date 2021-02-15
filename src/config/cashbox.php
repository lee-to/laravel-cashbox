<?php

return [
    "gateway" => [
        "class" => \App\CashBox\PaymentGateways\YooKassa::class,
        "credentials" => [
            "id" => env("CASHBOX_AUTH_ID", null),
            "key" => env("CASHBOX_AUTH_KEY", null),
        ],
        "currency" => "RUB",
        "email" => ""
    ],
    "notify" => [
        "default" => "telegram",
        "try_payment_message" => "",
        "new_payment_message" => "",
        "telegram" => [
            "chat_id" => "",
            "token" => "",
        ]
    ]
];
