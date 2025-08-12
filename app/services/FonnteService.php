<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $client;
    protected $token;

    public function __construct()
    {
        $this->client = new Client();
        $this->token = env('FONNTE_API_TOKEN');
    }

    public function sendMessage($phone, $message)
    {
        $url = 'https://api.fonnte.com/send';

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => $this->token, // tanpa Bearer
                ],
                'form_params' => [
                    'target' => $phone,
                    'message' => $message,
                ],
                'timeout' => 10, // biar request gak ngegantung lama
            ]);

            $result = json_decode($response->getBody(), true);

            // Log hasil respons untuk debugging
            Log::info('Fonnte sendMessage response', [
                'phone' => $phone,
                'result' => $result
            ]);

            return $result;
        } catch (RequestException $e) {
            $errorMessage = $e->hasResponse()
                ? $e->getResponse()->getBody()->getContents()
                : $e->getMessage();

            // Log error detail
            Log::error('Fonnte sendMessage failed', [
                'phone' => $phone,
                'error' => $errorMessage
            ]);

            return [
                'success' => false,
                'error' => $errorMessage
            ];
        }
    }
}
