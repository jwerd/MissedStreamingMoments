<?php

namespace App\Jobs;

use Alaouy\Youtube\Facades\Youtube;
use App\Events\StreamingUrlChanged;
use App\Exceptions\StreamerNotLiveException;
use App\Models\History;
use App\Services\YoutubeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckStreamer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $channel;
    public $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($channel)
    {
        $this->channel = $channel;
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
            // Get our channel
            $channel = $this->service->getChannel($this->channel->channelId);
            
            if(count($channel) === 0) {
                throw new StreamerNotLiveException;
            }

            $latest  = History::latestLink($this->channel->id);

            $videoId = $channel['videoId'];

            $history = History::where('key', $videoId)->firstOrFail();

            if(!$latest || $latest->key !== $videoId) {

                //$duration = $this->service->getVideoDurationById($videoId);

                $this->fireEvent([
                    'previousVideo' => $latest,
                    'videoId'       => $videoId,
                    'channelId'     => $this->channel->id,
                    'providerId'    => $this->channel->provider_id,
                ]);

                return;
            }
            Log::info("URL hasn't changed.  Will continue monitoring", [$this->channel, $videoId]);
        } catch(StreamerNotLiveException $e) {
            Log::info("The channel isn't currently live.", ['channelId' => $this->channel]);
            // Remove job from queue
            $this->delete();
        } catch(ModelNotFoundException $e) {
            Log::info("The currentl stream isn't stored yet.  Adding it.", ['channelId' => $this->channel, 'videoId' => $videoId]);
            History::create([
                'key'         => $videoId,
                'channel_id'  => $this->channel->id,
                'provider_id' => $this->channel->provider_id
            ]);
            $this->delete();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->delete();
        }
    }

    protected function fireEvent($params)
    {
        event(new StreamingUrlChanged($params));
    }
}