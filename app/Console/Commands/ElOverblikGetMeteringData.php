<?php

namespace App\Console\Commands;

use App\Services\GetMeteringData;
use Illuminate\Console\Command;

class ElOverblikGetMeteringData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eloverblik:get-meter-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get metering data from energioverblik';

    private $meteringDataService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GetMeteringData $meteringDataService)
    {
        $this->meteringDataService = $meteringDataService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        var_dump($this->meteringDataService->getData());
    }
}
