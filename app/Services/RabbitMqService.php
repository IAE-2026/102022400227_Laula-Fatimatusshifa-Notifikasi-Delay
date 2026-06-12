<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RabbitMqService
{
    public function publish($token, $payload)
    {
        return Http::withToken($token)
            ->post(
                'https://iae-sso.virtualfri.id/api/v1/messages/publish',
                [
                    'message' => $payload
                ]
            )
            ->json();
    }
}