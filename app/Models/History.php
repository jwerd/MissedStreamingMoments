<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'key',
        'duration',
        'channel_id',
        'provider_id'
    ];

    public static function latestLink($channelId)
    {
        return self::where('channel_id', $channelId)->latest()->first();
    }
}
