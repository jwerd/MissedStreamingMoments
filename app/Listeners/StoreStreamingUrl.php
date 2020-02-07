<?php

namespace App\Listeners;

use App\Models\History;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StoreStreamingUrl
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // Update final duration
        
        Log::info('Storing Streaming Url', [$event]);

        History::create([
            'key'        => $event->event['videoId'],
            'duration'   => $event->event['duration'],
            'channel_id' => $event->event['channelId'],
            'provider_id' => $event->event['providerId'],
        ]);
    }
}
