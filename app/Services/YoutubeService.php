<?php

namespace App\Services;

use Alaouy\Youtube\Facades\Youtube;
use App\Exceptions\StreamerNotLiveException;
use GuzzleHttp\Exception\GuzzleException;

class YoutubeService
{
    public $client;

    public $videos = 'https://www.youtube.com/channel/%s/videos?pbj=1';

    public $videoId;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    public function getChannel($channelId)
    {
        try {
            dump(sprintf($this->videos, $channelId));
            $response = $this->client->request('GET', sprintf($this->videos, $channelId), [
                'headers' => [
                    'X-YouTube-Client-Name'    => 1,
                    'X-YouTube-Client-Version' => '2.20200211.02.00',
                ]
            ]);
            $json = json_decode($response->getBody()->getContents());
            dd($json[1]->response
            ->contents
            ->twoColumnBrowseResultsRenderer
            ->tabs[1]);
        } catch(GuzzleException $e) {
            dd($e->getMessage());
        }

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

        $search = Youtube::getActivitiesByChannelId($params);
        if(!$search || count($search) === 0) {
            throw new StreamerNotLiveException();
        }

        return $search;
    }
}