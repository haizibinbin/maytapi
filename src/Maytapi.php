<?php

namespace Hbb\Maytapi;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Maytapi
{
    protected $product_id;
    protected $token;

    public function __construct($product_id, $token)
    {
        $this->product_id = $product_id;
        $this->token = $token;
    }

    public function http_requests($endpoint, $method, $phone_id, $json_data = [])
    {
        $client = new Client(['timeout' => 60]);
        try {
            $headers = [
                'x-maytapi-key' => $this->token,
            ];
            $options = [
                'headers' => $headers
            ];
            if ($json_data)
                $options['json'] = $json_data;

            $response = $client->request($method,
                "https://api.maytapi.com/api/$this->product_id/$phone_id/$endpoint",
                $options
            );

            $code = $response->getStatusCode(); // 200
            $body = $response->getBody();
            $stringBody = (string) $body;
            return ['code' => $code, 'body' => $stringBody];
        } catch (RequestException $exception) {
            return ['code' => 500, 'body' => $exception->getMessage()];
        }
    }

    /****************************************** Account Information Retrieval *****************************************/

    /**
     * @param $phone_id
     * @return array
     */
    public function product($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @return array
     */
    public function listPhones($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @param array $body
     * @return array
     */
    public function setWebhook($phone_id, array $body)
    {
        return $this->http_requests(__FUNCTION__, 'POST', $phone_id, $body);
    }

    /**
     * @param $phone_id
     * @param array $body
     * @return array
     */
    public function setAckPreference($phone_id, array $body)
    {
        return $this->http_requests(__FUNCTION__, 'POST', $phone_id, $body);
    }

    /**
     * @param $phone_id
     * @param array $body
     * @return array
     */
    public function addPhone($phone_id, array $body)
    {
        return $this->http_requests(__FUNCTION__, 'POST', $phone_id, $body);
    }

    /**
     * @param $phone_id
     * @return array
     */
    public function logs($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /****************************************** Session Information Getters *****************************************/

    /**
     * @param $phone_id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * ???????????????????????????????????????
     */
    public function screen($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }


    /**
     * @param $phone_id
     * @return array
     * ??????????????????????????????
     */
    public function status($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @return array
     * ??????????????????????????????????????? qrCode???
     */
    public function qrCode($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @return array
     * ???????????? whatsapp ????????????????????????
     */
    public function contacts($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }


    /****************************************** Message Sending Operations *****************************************/


    /**
     * @param $phone_id  // ???????????????ID
     * @param $to_number // ??????????????????????????????
     * @param $type  // ??????????????????
     * @param $message  // ????????????
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * ???????????????https://maytapi.com/whatsapp-api-documentation
     * ????????????
     */
    public function sendMessage($phone_id, $to_number, $type, $message, ...$params)
    {
        $json_data = compact('to_number', 'type', 'message');
        if ($params)
            $json_data = array_merge($json_data, $params[0]);

        $client = new Client(['timeout' => 60]);
        try {
            $headers = [
                'x-maytapi-key' => $this->token,
                'Content-Type' => 'application/json',
                'User-Agent' => 'PostmanRuntime/7.29.0',
            ];
            $response = $client->post( "https://api.maytapi.com/api/$this->product_id/$phone_id/sendMessage",
                [
                    'json' => $json_data,
                    'headers' => $headers
                ]
            );
            $code = $response->getStatusCode(); // 200
            $body = $response->getBody();
            $stringBody = (string) $body;
            return ['code' => $code, 'body' => $stringBody];
        } catch (RequestException $exception) {
            return ['code' => 500, 'body' => $exception->getMessage()];
        }
    }


    /****************************************** Business Catalog (Beta) *****************************************/


    /**
     * @param $phone_id
     * @return array
     * ?????????????????????????????????????????? ???????????? productId ?????????????????????????????????
     */
    public function catalog($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /****************************************** Group Chat Operations *****************************************/

    /**
     * @param $phone_id
     * @param $name
     * @param array $numbers
     * @return array
     * ???????????? webhook ???????????????????????????????????? true ??? false???
     */
    public function createGroup($phone_id, $name, array $numbers)
    {
        return $this->http_requests(__FUNCTION__, 'POST', $phone_id, compact('name', 'numbers'));
    }

    /**
     * @param $phone_id
     * @return array
     */
    public function getGroups($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @param $conversation_id
     * @return array
     */
    public function getGroupsInfo($phone_id, $conversation_id)
    {
        return $this->http_requests("getGroups/$conversation_id", 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @param $conversation_id
     * @param $image
     * @return array
     * ??????????????????????????????????????????????????????
     * ?????????????????????????????????????????????????????? conversation_id ?????????
     * ???????????????????????????????????????????????????????????????????????? 512x512 ?????????
     */
    public function setProfileImage($phone_id, $conversation_id, $image)
    {
        return $this->http_requests(__FUNCTION__, 'POST', $phone_id, compact('conversation_id', 'image'));
    }


    /**
     * @param $phone_id
     * @param $conversation_id
     * @param array $config
     * @return array
     * ??????????????????????????????
     * ????????? conversation_id?????????????????? /getGroups ?????????
     * ????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
     * ???????????????????????????????????????????????????admins?????????all??????
     * ??????????????????/?????????
     * ???????????????????????? ?????????????????????????????????????????????????????????????????????????????????
     */
    public function groupConfig($phone_id, $conversation_id, array $config)
    {
        return $this->http_requests('group/config', 'POST', $phone_id, compact('conversation_id', 'config'));
    }

    /**
     * @param $phone_id
     * @param $conversation_id
     * @param $number
     * @return array
     */
    public function groupAdd($phone_id, $conversation_id, $number)
    {
        return $this->http_requests('group/add', 'POST', $phone_id, compact('conversation_id', 'number'));
    }

    /**
     * @param $phone_id
     * @param $conversation_id
     * @param $number
     * @return array
     */
    public function groupRemove($phone_id, $conversation_id, $number)
    {
        return $this->http_requests('group/remove', 'POST', $phone_id, compact('conversation_id', 'number'));
    }

    /**
     * @param $phone_id
     * @param $conversation_id
     * @param $number
     * @return array
     */
    public function groupPromote($phone_id, $conversation_id, $number)
    {
        return $this->http_requests('group/promote', 'POST', $phone_id, compact('conversation_id', 'number'));
    }

    /**
     * @param $phone_id
     * @param $conversation_id
     * @param $number
     * @return array
     */
    public function groupDemote($phone_id, $conversation_id, $number)
    {
        return $this->http_requests('group/demote', 'POST', $phone_id, compact('conversation_id', 'number'));
    }

    /****************************************** Queue Operations *****************************************/

    /**
     * @param $phone_id
     * @return array
     */
    public function queue($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @return array
     */
    public function purgeQueue($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /****************************************** Session Controlling Operations *****************************************/

    /**
     * @param $phone_id
     * @return array
     */
    public function redeploy($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @return array
     */
    public function backup($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @param array $body
     * @return array
     */
    public function restore($phone_id, array $body)
    {
        return $this->http_requests(__FUNCTION__, 'POST', $phone_id, $body);
    }

    /**
     * @param $phone_id
     * @return array
     */
    public function factoryreset($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @return array
     */
    public function logout($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @param $body
     * @return array
     * ???body ???????????????????????????????????????
     */
    public function config($phone_id, array $body = [])
    {
        $method = empty($body) ? 'GET' : 'POST';
        return $this->http_requests(__FUNCTION__, $method, $phone_id, $body);
    }

}