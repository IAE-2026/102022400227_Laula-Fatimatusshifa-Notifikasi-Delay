<?php

namespace Tests\Feature;

use App\Models\DelayNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DelayNotificationApiTest extends TestCase
{
    use RefreshDatabase;

    private $apiKey = '102022400227';

    public function test_trip_status_requires_api_key(): void
    {
        $response = $this->getJson('/api/v1/trips/1/status');

        $response
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthorized',
                'errors' => null
            ]);
    }

    public function test_trip_status_with_valid_api_key(): void
    {
        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey,
        ])->getJson('/api/v1/trips/1/status');

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'trip_id' => '1',
                    'status' => 'delayed',
                ],
                'meta' => [
                    'service_name' => 'Delay Notification Service',
                    'api_version' => 'v1'
                ]
            ]);
    }

    public function test_get_collection_requires_api_key(): void
    {
        $response = $this->getJson('/api/v1/delay-notifikasi');
        $response->assertStatus(401);
    }

    public function test_get_collection_success(): void
    {
        DelayNotification::create([
            'trip_id' => 'TRIP123',
            'route_name' => 'Jakarta - Bandung',
            'delay_minutes' => 45,
            'delay_reason' => 'Sinyal Bermasalah',
            'passenger_name' => 'Budi',
            'passenger_email' => 'budi@example.com',
            'notification_status' => 'pending'
        ]);

        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey,
        ])->getJson('/api/v1/delay-notifikasi');

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'status' => 'success',
                'message' => 'Delay notifications retrieved successfully',
                'meta' => [
                    'service_name' => 'Delay Notification Service',
                    'api_version' => 'v1'
                ]
            ]);
    }

    public function test_store_validation_failures(): void
    {
        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey,
        ])->postJson('/api/v1/delay-notifikasi', []); // Empty data

        $response
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation failed'
            ]);
    }

    public function test_store_invalid_datetime_format(): void
    {
        $payload = [
            'trip_id' => 'TRIP456',
            'route_name' => 'Surabaya - Malang',
            'departure_time' => 'invalid-datetime-format',
            'delay_minutes' => 15,
            'delay_reason' => 'Perbaikan Rel',
            'passenger_name' => 'Andi',
            'passenger_email' => 'andi@example.com'
        ];

        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey,
        ])->postJson('/api/v1/delay-notifikasi', $payload);

        $response
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'departure_time' => [
                        'Format harus Y-m-d H:i:s atau ISO-8601.'
                    ]
                ]
            ]);
    }

    public function test_store_iso8601_datetime_format_success(): void
    {
        $payload = [
            'trip_id' => 'TRIP456',
            'route_name' => 'Surabaya - Malang',
            'departure_time' => '2026-06-25T12:00:00Z',
            'delay_minutes' => 15,
            'delay_reason' => 'Perbaikan Rel',
            'passenger_name' => 'Andi',
            'passenger_email' => 'andi@example.com'
        ];

        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey,
        ])->postJson('/api/v1/delay-notifikasi', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.departure_time', '2026-06-25 12:00:00');
    }

    public function test_store_success(): void
    {
        $payload = [
            'trip_id' => 'TRIP456',
            'route_name' => 'Surabaya - Malang',
            'delay_minutes' => 15,
            'delay_reason' => 'Perbaikan Rel',
            'passenger_name' => 'Andi',
            'passenger_email' => 'andi@example.com'
        ];

        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey,
        ])->postJson('/api/v1/delay-notifikasi', $payload);

        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Delay notification created successfully',
                'data' => [
                    'trip_id' => 'TRIP456',
                    'route_name' => 'Surabaya - Malang',
                    'notification_status' => 'pending'
                ],
                'meta' => [
                    'service_name' => 'Delay Notification Service',
                    'api_version' => 'v1'
                ]
            ]);
    }

    public function test_show_not_found(): void
    {
        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey,
        ])->getJson('/api/v1/delay-notifikasi/999');

        $response
            ->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Notification not found',
                'errors' => null
            ]);
    }

    public function test_show_success(): void
    {
        $notification = DelayNotification::create([
            'trip_id' => 'TRIP789',
            'route_name' => 'Semarang - Solo',
            'delay_minutes' => 60,
            'delay_reason' => 'Cuaca Buruk',
            'passenger_name' => 'Cici',
            'passenger_email' => 'cici@example.com',
            'notification_status' => 'pending'
        ]);

        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey,
        ])->getJson('/api/v1/delay-notifikasi/' . $notification->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'trip_id' => 'TRIP789',
                    'passenger_name' => 'Cici'
                ],
                'meta' => [
                    'service_name' => 'Delay Notification Service',
                    'api_version' => 'v1'
                ]
            ]);
    }

    public function test_send_not_found(): void
    {
        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey,
        ])->postJson('/api/v1/delay-notifikasi/send', ['id' => 999]);

        $response
            ->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Notification not found',
                'errors' => null
            ]);
    }

    public function test_send_success(): void
    {
        $notification = DelayNotification::create([
            'trip_id' => 'TRIP789',
            'route_name' => 'Semarang - Solo',
            'delay_minutes' => 60,
            'delay_reason' => 'Cuaca Buruk',
            'passenger_name' => 'Cici',
            'passenger_email' => 'cici@example.com',
            'notification_status' => 'pending'
        ]);

        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey,
        ])->postJson('/api/v1/delay-notifikasi/send', ['id' => $notification->id]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Notification sent successfully',
                'data' => [
                    'id' => $notification->id,
                    'notification_status' => 'sent'
                ],
                'meta' => [
                    'service_name' => 'Delay Notification Service',
                    'api_version' => 'v1'
                ]
            ]);
    }

    public function test_graphql_trip_status(): void
    {
        $response = $this->postJson('/graphql', [
            'query' => '
                {
                    tripStatus(id: 1) {
                        trip_id
                        status
                    }
                }
            ',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'tripStatus' => [
                        'trip_id' => '1',
                        'status' => 'delayed',
                    ],
                ],
            ]);
    }

    public function test_swagger_documentation_is_accessible(): void
    {
        $response = $this->get('/api/documentation');

        $response->assertStatus(200);
    }
}