<?php

namespace App\Jobs;

use Alaouy\Youtube\Facades\Youtube;
use App\Exceptions\StreamerNotLiveException;
use App\Services\YoutubeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckStreamer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $channel_id;
    public $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($channel_id)
    {
        $this->channel_id = $channel_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->service = new YoutubeService;

        try {
            $videoId = $this->service->getCurrentVideoIdByChannel($this->channel_id);

            $duration = $this->service->getVideoDurationById($videoId);
            
            dd($this->channel_id, $videoId, $duration);
        } catch(StreamerNotLiveException $e) {
            Log::info("The channel isn't currently live.", ['channelId' => $this->channel_id]);
            // Remove job from queue
            $this->delete();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

}