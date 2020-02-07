<?php

namespace App\Services;

use Alaouy\Youtube\Facades\Youtube;
use App\Exceptions\StreamerNotLiveException;

class YoutubeService
{
    public $params = [
        'type'      => 'video',
        'eventType' => 'live',
        'maxResults'=> 1,
        'part'      => 'snippet',
    ];

    public $videoId;

    public function __construct()
    {
        
    }

    public function getByChannel($channelId)
    {
        $params = array_merge($this->params, [
            'channelId' => $channelId,
        ]);
        
        return $this->search($params);
    }

    public function getCurrentVideoIdByChannel($channelId)
    {
        $data = $this->getByChannel($channelId)[0];

        return $data->id->videoId;
    }

    public function getVideoDetailsById($videoId)
    {
        return Youtube::getVideoInfo($videoId);
    }

    public function getVideoDurationById($videoId)
    {
        return convertYoutubeDuration($this->getVideoDetailsById($videoId)->contentDetails->duration);
    }

    public function getByUsername($username)
    {
        // @todo
    }

    protected function search($params)
    {
        $search = Youtube::searchAdvanced($params);
        if(!$search || count($search) === 0) {
            throw new StreamerNotLiveException();
        }

        return $search;
    }
}