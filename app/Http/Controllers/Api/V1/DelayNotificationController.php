<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DelayNotification;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DelayNotificationController extends Controller
{
    #[OA\Get(
        path: '/api/v1/trips/{id}/status',
        summary: 'Get trip delay status',
        tags: ['Delay Notification'],
        security: [
            ['ApiKeyAuth' => []]
        ],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Trip ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            )
        ]
    )]
    public function tripStatus($id)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Trip status retrieved successfully',
            'data' => [
                'trip_id' => $id,
                'status' => 'delayed'
            ],
            'meta' => [
                'service_name' => 'Delay Notification Service',
                'api_version' => 'v1'
            ]
        ], 200);
    }

    #[OA\Get(
        path: '/api/v1/delay-notifikasi',
        summary: 'Get all delay notifications',
        tags: ['Delay Notification'],
        security: [
            ['ApiKeyAuth' => []]
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            )
        ]
    )]
    #[OA\Get(
        path: '/api/v1/trips',
        summary: 'Get all delay notifications (trips resource alias)',
        tags: ['Delay Notification'],
        security: [
            ['ApiKeyAuth' => []]
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            )
        ]
    )]
    public function index()
    {
        $notifications = DelayNotification::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Delay notifications retrieved successfully',
            'data' => $notifications,
            'meta' => [
                'service_name' => 'Delay Notification Service',
                'api_version' => 'v1'
            ]
        ], 200);
    }

    #[OA\Post(
        path: '/api/v1/delay-notifikasi',
        summary: 'Create a new delay notification',
        tags: ['Delay Notification'],
        security: [
            ['ApiKeyAuth' => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['trip_id', 'route_name', 'delay_minutes', 'delay_reason', 'passenger_name', 'passenger_email'],
                properties: [
                    new OA\Property(property: 'trip_id', type: 'string', example: 'TRIP001'),
                    new OA\Property(property: 'route_name', type: 'string', example: 'Jakarta - Bandung'),
                    new OA\Property(property: 'departure_time', type: 'string', format: 'date-time', example: '2026-06-25T12:00:00Z', nullable: true),
                    new OA\Property(property: 'delay_minutes', type: 'integer', example: 30),
                    new OA\Property(property: 'delay_reason', type: 'string', example: 'Weather issue'),
                    new OA\Property(property: 'passenger_name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'passenger_email', type: 'string', format: 'email', example: 'john.doe@example.com')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created successfully'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed'
            )
        ]
    )]
    #[OA\Post(
        path: '/api/v1/trips',
        summary: 'Create a new delay notification (trips resource alias)',
        tags: ['Delay Notification'],
        security: [
            ['ApiKeyAuth' => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['trip_id', 'route_name', 'delay_minutes', 'delay_reason', 'passenger_name', 'passenger_email'],
                properties: [
                    new OA\Property(property: 'trip_id', type: 'string', example: 'TRIP001'),
                    new OA\Property(property: 'route_name', type: 'string', example: 'Jakarta - Bandung'),
                    new OA\Property(property: 'departure_time', type: 'string', format: 'date-time', example: '2026-06-25T12:00:00Z', nullable: true),
                    new OA\Property(property: 'delay_minutes', type: 'integer', example: 30),
                    new OA\Property(property: 'delay_reason', type: 'string', example: 'Weather issue'),
                    new OA\Property(property: 'passenger_name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'passenger_email', type: 'string', format: 'email', example: 'john.doe@example.com')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created successfully'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed'
            )
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required',
            'route_name' => 'required',
            'departure_time' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    try {
                        \Carbon\Carbon::parse($value);
                    } catch (\Exception $e) {
                        $fail('Format harus Y-m-d H:i:s atau ISO-8601.');
                    }
                }
            ],
            'delay_minutes' => 'required|integer',
            'delay_reason' => 'required',
            'passenger_name' => 'required',
            'passenger_email' => 'required|email'
        ], [
            'departure_time' => 'Format harus Y-m-d H:i:s atau ISO-8601.'
        ]);

        if (isset($validated['departure_time']) && $validated['departure_time'] !== null) {
            $validated['departure_time'] = \Carbon\Carbon::parse($validated['departure_time'])->format('Y-m-d H:i:s');
        }

        $notification = DelayNotification::create([
            ...$validated,
            'notification_status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Delay notification created successfully',
            'data' => $notification,
            'meta' => [
                'service_name' => 'Delay Notification Service',
                'api_version' => 'v1'
            ]
        ], 201);
    }

    #[OA\Post(
        path: '/api/v1/delay-notifikasi/send',
        summary: 'Send a delay notification',
        tags: ['Delay Notification'],
        security: [
            ['ApiKeyAuth' => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id'],
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Notification sent successfully'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 404,
                description: 'Notification not found'
            )
        ]
    )]
    public function send(Request $request)
    {
        $notification = DelayNotification::find($request->id);

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found',
                'errors' => null
            ], 404);
        }

        $notification->update([
            'notification_status' => 'sent',
            'sent_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification sent successfully',
            'data' => $notification,
            'meta' => [
                'service_name' => 'Delay Notification Service',
                'api_version' => 'v1'
            ]
        ], 200);
    }

    #[OA\Get(
        path: '/api/v1/delay-notifikasi/{id}',
        summary: 'Get details of a delay notification',
        tags: ['Delay Notification'],
        security: [
            ['ApiKeyAuth' => []]
        ],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Notification ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 404,
                description: 'Notification not found'
            )
        ]
    )]
    #[OA\Get(
        path: '/api/v1/trips/{id}',
        summary: 'Get details of a delay notification (trips resource alias)',
        tags: ['Delay Notification'],
        security: [
            ['ApiKeyAuth' => []]
        ],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Notification ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 404,
                description: 'Notification not found'
            )
        ]
    )]
    public function show($id)
    {
        $notification = DelayNotification::find($id);

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found',
                'errors' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Notification detail retrieved successfully',
            'data' => $notification,
            'meta' => [
                'service_name' => 'Delay Notification Service',
                'api_version' => 'v1'
            ]
        ], 200);
    }
}