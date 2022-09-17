<?php

namespace App\Console\Commands;

use App\Services\GetMeteringData;
use App\Services\GetSpotPrices;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateUpcommingInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:invoice {refresh_token?} {--start-date=2022-09-01} {--end-date=2022-10-01} {--price-area=DK2}';

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
        $start_date = $this->option('start-date');
        $end_date = $this->option('end-date');
        if(Carbon::parse($end_date)->greaterThan(Carbon::now()->startOfDay())) {
            $end_date = Carbon::now()->startOfDay()->toDateString();
        }
        $price_area = $this->option('price-area');
        $meterData = $this->meteringDataService->getData($refresh_token, $start_date, $end_date);
        $spotPrices = new GetSpotPrices();
        $prices = $spotPrices->getData($start_date, $end_date, $price_area);


        list($subscriptions, $tariffs) = $this->meteringDataService->getCharges($refresh_token);

        $sum = 0;

        foreach ($meterData as $hour => $consumption) {
            //echo $hour . ': ' . $consumption . PHP_EOL;
            foreach ($tariffs as $tariff) {
                if (count($tariff['prices']) > 1) {
                    $theTarif = $tariff['prices'][Carbon::parse($hour)->hour]['price'] * $consumption;
                } else {
                    $theTarif = $tariff['prices'][0]['price'] * $consumption;
                }
                //echo $tariff['name'] . ': ' . $theTarif . PHP_EOL;
                $sum = $sum + $theTarif;
            }

            $overhead = $consumption * 0.015;
            $spottotal = $consumption * ($prices[$hour] / 1000);
            $sum = $sum + $overhead + $spottotal;
        }

        foreach ($subscriptions as $subscription) {
            $sum = $sum + $subscription['price'];
        }

        $elabonnement = 23.20;

        $sum = $sum + $elabonnement;

        $summaSummarum = $sum * 1.25;

        $summaSummarum = str_replace('.', ',', round($summaSummarum, 2));

        echo 'kr. ' . $summaSummarum . PHP_EOL;
    }
}
