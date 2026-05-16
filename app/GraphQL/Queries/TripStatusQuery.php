<?php

namespace App\GraphQL\Queries;

class TripStatusQuery
{
    public function resolve($_, array $args)
    {
        return [
            'trip_id' => $args['id'],
            'status' => 'delayed',
        ];
    }
}