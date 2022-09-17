<?php

namespace App\Services;

use GuzzleHttp\Client;

class GetSpotPrices
{
    public function getData()
    {
        $client = new Client();
        $verb = 'GET';
        $parameters = ['limit' => 744, 'start' => '2022-09-01', 'filter' => '{"PriceArea":"DK2"}', 'columns' => 'HourDK,SpotPriceDKK'];
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