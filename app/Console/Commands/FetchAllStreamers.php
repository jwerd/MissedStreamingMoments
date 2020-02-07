<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CheckStreamer;


class FetchAllStreamers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streamer:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all streamers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Check streamer by channel id
        //$channel = 'UCo8wWQvRSoKL57vjv4vyXQw'; // CaptainContent
        $channel = 'UCQXD6LaVAICsyam10YjsUSw'; // Atila
        CheckStreamer::dispatch($channel);
    }
}
