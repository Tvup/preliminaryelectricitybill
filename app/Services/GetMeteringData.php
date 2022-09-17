<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Tvup\ElOverblikApi\ElOverblikApiException;

class GetMeteringData
{


    /**
     * @var mixed
     */
    private $energiOverblikApi;

    private $meteringPoint;


    public function getData($refreshToken = null, $start_date, $end_date)
    {
        $energiOverblikApi = $this->getEloverblikApi($refreshToken);

        if ($refreshToken) {
            //NOP
        } else {
            $refreshToken = Config::get('services.energioverblik.refresh_token');
        }

        $energiOverblikApi->token($refreshToken);

        $meteringPointId = $this->getMeteringPoint($refreshToken);

        $response = null;

        try {
            if(!$start_date) {
                $start_date = Carbon::now()->startOfMonth()->toDateString();
            }
            if(!$end_date) {
                $end_date = Carbon::now()->toDateString();
            }
            $response = $energiOverblikApi->getHourTimeSeriesFromMeterData($start_date, $end_date, $meteringPointId);
        } catch (ElOverblikApiException $e) {
            var_dump($e->getErrors());
        }

        return $response;
    }

    public function getMeteringPoint(string $refresh_token = null)
    {
        if(!$this->meteringPoint) {
            $energiOverblikApi = $this->getEloverblikApi($refresh_token);

            if ($refresh_token) {
                //NOP
            } else {
                $refresh_token = Config::get('services.energioverblik.refresh_token');
            }

            $energiOverblikApi->token($refresh_token);

            $response = null;

            try {
                $response = $energiOverblikApi->getFirstMeteringPoint();
            } catch (ElOverblikApiException $e) {
            }
            $this->meteringPoint = $response;
        }
        return $this->meteringPoint;
    }

    /**
     * @param $refreshToken
     */
    private function getEloverblikApi($refreshToken = null)
    {
        if (!$this->energiOverblikApi) {
            $this->energiOverblikApi = app()->makeWith('Tvup\ElOverblikApi\ElOverblikApiInterface', [
                'refreshToken' => $refreshToken
            ]);
        }
        return $this->energiOverblikApi;
    }

    public function getCharges(string $refresh_token = null)
    {
        $energiOverblikApi = $this->getEloverblikApi($refresh_token);

        if ($refresh_token) {
            //NOP
        } else {
            $refresh_token = Config::get('services.energioverblik.refresh_token');
        }

        $energiOverblikApi->token($refresh_token);

        $meteringPointId = $this->getMeteringPoint($refresh_token);

        $response = null;

        try {
            list($subscriptions, $tariffs) = $energiOverblikApi->getCharges($meteringPointId);
        } catch (ElOverblikApiException $e) {
            var_dump($e->getErrors());
        }

        return array($subscriptions, $tariffs);
    }
}