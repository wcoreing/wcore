<?php
/**
 * Created by PhpStorm.
 * User: weining
 * Date: 2021/6/2
 * Time: 下午 8:00
 */

namespace app\wcore;

use GuzzleHttp\RequestOptions;

class Client extends WObject
{

    protected $client;

    function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            RequestOptions::VERIFY => false,
        ]);
    }

    public function Get($url, $paramData = [], $headers = [])
    {
        Log::Instant()->Write([$url, $paramData]);
        try {
            $res = $this->client->request('GET', $url, [
                RequestOptions::QUERY => $paramData,
                RequestOptions::HEADERS => $headers
            ]);
            $data = $res->getBody();
            $data = json_decode($data, true);
        } catch (\Exception $e) {
            Log::Instant()->Write("throw exception");
            Log::Instant()->Write($e->getMessage(), "exception/log");
            $data = false;
        }
        Log::Instant()->Write($data);
        return $data;
    }

    public function Post($url, $paramData,$headers=[])
    {
        Log::Instant()->Write([$url, $paramData]);
        try {
            $res = $this->client->request('POST', $url, [
                RequestOptions::FORM_PARAMS => $paramData,
                RequestOptions::HEADERS => $headers
            ]);
            $data = $res->getBody();
            $data = json_decode($data, true);
        } catch (\Exception $e) {
            Log::Instant()->Write("throw exception");
            Log::Instant()->Write($e->getMessage(), "exception/log");
            $data = false;
        }
        Log::Instant()->Write($data);
        return $data;
    }

    public function PostJson($url, $paramData, $headers = [])
    {
        Log::Instant()->Write([$url, $paramData]);
        try {
            $_headers = [
                'Content-Type' => 'application/json',
            ];
            $headers = array_merge($headers, $_headers);
            $options = [
                RequestOptions::JSON => $paramData,
                RequestOptions::HEADERS => $headers,
            ];
            $res = $this->client->request('POST', $url, $options);
            $data = $res->getBody();
            $data = json_decode($data, true);
        } catch (\Exception $e) {
            Log::Instant()->Write("throw exception");
            Log::Instant()->Write($e->getMessage(), "exception/log");
            $data = false;
        }

        Log::Instant()->Write($data);
        return $data;
    }
}
