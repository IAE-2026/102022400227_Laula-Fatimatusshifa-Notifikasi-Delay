<?php

namespace Tests\Feature;

use Tests\TestCase;

class DelayNotificationApiTest extends TestCase
{
    public function test_trip_status_requires_api_key(): void
    {
        $response = $this->getJson('/api/v1/trips/1/status');

        $response
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthorized',
            ]);
    }

    public function test_trip_status_with_valid_api_key(): void
    {
        $response = $this->withHeaders([
            'X-IAE-KEY' => '102022400227',
        ])->getJson('/api/v1/trips/1/status');

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'trip_id' => '1',
                    'status' => 'delayed',
                ],
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