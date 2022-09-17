<?php

namespace App\Console\Commands;

use App\Services\GetSpotPrices;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class EnergiDataGetPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'energidata:get-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get spot prices from energi data';

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
        $new = new GetSpotPrices();
        var_dump($new->getData());
    }
}
