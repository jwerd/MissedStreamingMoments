<?php

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $channels = config('channels');

        collect($channels)->map(function ($channelId, $id) {
            Channel::create([
                'id'        => $id,
                'channelId' => $channelId
            ]);
        });
    }
}
