<?php
namespace App\Http\Services;

use Error;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ShortUrlService implements ShortUrlInterfaceService
{
    protected $client;
    public function __construct()
    {
        $this->client = new Client();   
    }
    
    public function makeShortUrl($url)
    {
        try {
            $accesstoken = env('PicSee_Access_Token');
            $data = [
                'url' => $url
            ];
            $response = $this->client->request(
                'POST',
                "https://api.pics.ee/v1/links/?access_token=$accesstoken",
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => json_encode($data)
                ]
                );
            $contents = $response->getBody()->getContents();

            Log::channel('url_shorten')->info('responseData', ['data' => $contents]); // 儲存Log

            $contents = json_decode($contents);
            $url = $contents->data->picseeUrl;
        } catch (\Throwable $th) {
            report($th);
            return $url;
        }
        return $url;
    }
}