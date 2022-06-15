<?php

namespace Hbb\Maytapi;

//use GuzzleHttp\Client;

class Maytapi
{
    protected $product_id;
    protected $token;

//    public function sendMessage($phone_id)
//    {
//        $client = new Client();
//        $response = $client->request('GET', "https://api.maytapi.com/api/{{$this->product_id}}/{{$phone_id}}/sendMessage");
//
//    }

    public function desc()
    {
        echo "this is test message";
    }

}