<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DelayNotification;
use App\Services\SoapAuditService;
use App\Services\RabbitMqService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DelayNotificationController extends Controller
{
    #[OA\Get(
        path: '/api/v1/trips/{id}/status',
        summary: 'Get trip delay status',
        tags: ['Delay Notification'],
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
                'service_name' => 'Notification-Delay',
                'api_version' => 'v1'
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required',
            'route_name' => 'required',
            'delay_minutes' => 'required|integer',
            'delay_reason' => 'required',
            'passenger_name' => 'required',
            'passenger_email' => 'required|email'
        ]);

        $notification = DelayNotification::create([
            ...$validated,
            'notification_status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Delay notification created successfully',
            'data' => $notification,
            'meta' => [
                'service_name' => 'Notification-Delay',
                'api_version' => 'v1'
            ]
        ], 201);
    }

    public function send(
        Request $request,
        SoapAuditService $soap,
        RabbitMqService $rabbit
    )
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

        $token = str_replace(
            'Bearer ',
            '',
            $request->header('Authorization')
        );

        $soapResponse = $soap->sendAudit(
    $token,
    json_encode([
        'notification_id' => $notification->id,
        'trip_id' => $notification->trip_id,
        'status' => 'sent'
    ])
);

        preg_match(
            '/<iae:ReceiptNumber>(.*?)<\/iae:ReceiptNumber>/',
            $soapResponse,
            $matches
        );

        $receiptNumber = $matches[1] ?? null;

        $notification->update([
            'receipt_number' => $receiptNumber
        ]);

        $rabbitResponse = $rabbit->publish(
            $token,
            [
                'event' => 'delay_notification_sent',
                'notification_id' => $notification->id,
                'trip_id' => $notification->trip_id,
                'delay_minutes' => $notification->delay_minutes,
                'status' => 'sent'
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Notification sent successfully',
            'receipt_number' => $receiptNumber,
            'rabbitmq_response' => $rabbitResponse,
            'data' => $notification,
            'meta' => [
                'service_name' => 'Notification-Delay',
                'api_version' => 'v1'
            ]
        ], 200);
    }

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
                'service_name' => 'Notification-Delay',
                'api_version' => 'v1'
            ]
        ], 200);
    }
}