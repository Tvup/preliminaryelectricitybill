<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Tvup\EwiiApi\EwiiApiException;
use Tvup\EwiiApi\EwiiApiInterface;

class EwiiGetConsumptionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ewii:get-consumptiondata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all consumptions from Ewii for me';

    /**
     * @var EwiiApiInterface
     */
    private $ewiiApi;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EwiiApiInterface $ewiiApi)
    {
        $this->ewiiApi = $ewiiApi;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ewiiApi = $this->ewiiApi;

        $email = Config::get('services.ewii.email');
        $password = Config::get('services.ewii.password');

        $response = null;

        try {
            $ewiiApi->login($email, $password);
            $response = $ewiiApi->getAddressPickerViewModel();
            $ewiiApi->setSelectedAddressPickerElement($response);
            $response = $ewiiApi->getConsumptionMeters();
            $response = $ewiiApi->getConsumptionData('csv', $response);
        } catch (EwiiApiException $e) {
            var_dump($e->getErrors());
        }

        if ($response) {
            print_r($response[0]);
        }

    }
}
