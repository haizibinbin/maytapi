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
        $client = new Client(['timeout' => 60, 'verify' => false]);
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
     * 返回当前会话中的屏幕截图。
     */
    public function screen($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }


    /**
     * @param $phone_id
     * @return array
     * 返回当前会话的状态。
     */
    public function status($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @return array
     * 如果有，则返回当前会话中的 qrCode。
     */
    public function qrCode($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @return array
     * 返回当前 whatsapp 实例中的联系人。
     */
    public function contacts($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }


    /****************************************** Message Sending Operations *****************************************/


    /**
     * @param $phone_id  // 绑定的手机ID
     * @param $to_number // 发送消息到哪一个号吗
     * @param $type  // 发送消息类型
     * @param $message  // 消息内容
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * 参考文档：https://maytapi.com/whatsapp-api-documentation
     * 发送消息
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
     * 返回业务资料的目录产品信息。 您只需要 productId 即可将产品发送给用户。
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
     * 将用于从 webhook 获取确认消息的选项设置为 true 或 false。
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
     * 更改群组对话或您自己的个人资料图片。
     * 要更改您自己的个人资料图片，您需要将 conversation_id 留空。
     * 您需要使用方形图像以获得更好的视图。我们建议使用 512x512 图像。
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
     * 更改群组对话的配置。
     * 要了解 conversation_id，您可以使用 /getGroups 端点。
     * 您可以更改谁可以编辑组信息（编辑），可以发送消息（发送）和启用消失消息（消失）。
     * 对于编辑和发送配置，您可以使用值“admins”或“all”。
     * 消失只接受真/假值。
     * 配置值是可选的。 如果您只想更改配置，则应仅在请求正文中使用该配置的键。
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
     * 由于logout API功能关闭，所以以后退出登录可以用 clear
     */
    public function logout($phone_id)
    {
        return $this->http_requests('clear', 'GET', $phone_id);
    }


    /**
     * @param $phone_id
     * @return array
     * 由于logout API功能关闭，所以以后退出登录可以用 clear
     */
    public function clear($phone_id)
    {
        return $this->http_requests(__FUNCTION__, 'GET', $phone_id);
    }

    /**
     * @param $phone_id
     * @param $body
     * @return array
     * 有body 数据为修改，否则为获取信息
     */
    public function config($phone_id, array $body = [])
    {
        $method = empty($body) ? 'GET' : 'POST';
        return $this->http_requests(__FUNCTION__, $method, $phone_id, $body);
    }

}
