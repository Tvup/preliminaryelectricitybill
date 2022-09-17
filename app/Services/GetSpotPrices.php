<?php

namespace App\Services;

use GuzzleHttp\Client;

class GetSpotPrices
{
    public function getData(string $start_date, string $end_date, string $price_area)
    {
        $client = new Client();
        $verb = 'GET';
        $parameters = ['start' => $start_date, 'end' => $end_date, 'filter' => '{"PriceArea":"' . $price_area . '"}', 'columns' => 'HourDK,SpotPriceDKK'];
        $parameters = '?' . http_build_query($parameters);
        $url = 'https://api.energidataservice.dk/dataset/Elspotprices' . $parameters;

        $response = $client->request($verb, $url);
        $response = $response->getBody()->getContents();
        $response = json_decode($response, true);

        $array = array_reverse($response['records']);
        $new_array = array();
        foreach ($array as $data) {
            $new_array[$data['HourDK']] = $data['SpotPriceDKK'];
        }
        return $new_array;
    }
}