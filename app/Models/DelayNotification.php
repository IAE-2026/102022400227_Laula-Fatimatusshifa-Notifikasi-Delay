<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DelayNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'route_name',
        'departure_time',
        'delay_minutes',
        'delay_reason',
        'notification_status',
        'passenger_name',
        'passenger_email',
        'sent_at',
        'receipt_number'
    ];
}