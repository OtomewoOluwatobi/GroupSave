<?php

return [
    'url_send' => 'https://exp.host/--/api/v2/push/send',

    'log' => [
        'enabled' => env('EXPO_PUSH_LOG', false),
        'driver' => [
            'type' => \Trin4ik\LaravelExpoPush\Drivers\ExpoPushNotificationDriverModel::class,
            'instance' => \Trin4ik\LaravelExpoPush\Models\ExpoPushNotification::class
        ]
    ]
];
