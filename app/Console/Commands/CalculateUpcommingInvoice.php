<?php

namespace App\Console\Commands;

use App\Services\GetMeteringData;
use App\Services\GetSpotPrices;
use Illuminate\Console\Command;

class CalculateUpcommingInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:invoice {refresh_token?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var GetMeteringData
     */
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
        $refresh_token = $this->argument('refresh_token');

        $meterData = $this->meteringDataService->getData($refresh_token);
        $spotPrices = new GetSpotPrices();
        $prices = $spotPrices->getData();

        list($subscriptions, $tariffs) = $this->meteringDataService->getCharges($refresh_token);

        $sum = 0;

        foreach ($meterData as $hour => $consumption) {
            //echo $hour . ': ' . $consumption . PHP_EOL;
            foreach ($tariffs as $tariff) {
                $theTarif = $tariff['prices'][0]['price'] * $consumption;
                //echo $tariff['name'] . ': ' . $theTarif . PHP_EOL;
                $sum = $sum + $theTarif;
            }

            $overhead = $consumption * 0.015;
            $spottotal = $consumption * ($prices[$hour]/1000);
            $sum = $sum + $overhead + $spottotal;
        }

        foreach ($subscriptions as $subscription) {
            $sum = $sum + $subscription['price'];
        }

        $elabonnement = 23.20;

        $sum = $sum + $elabonnement;

        $summaSummarum = $sum * 1.25;

        $summaSummarum = str_replace('.',',', round($summaSummarum, 2));

        echo 'kr. ' . $summaSummarum . PHP_EOL;
    }
}
