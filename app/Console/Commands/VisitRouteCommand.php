<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;


class VisitAddMediaRoute extends Command
{
    protected $signature = 'route:call-add-media';
    protected $description = 'Call the add-media route via command line';

    public function handle()
    {
        $response = Http::get(url('add-media'));

        if ($response->successful()) {
            $this->info('Route called successfully!');
        } else {
            $this->error('Failed to call the route.');
        }

        return 0;
    }
}