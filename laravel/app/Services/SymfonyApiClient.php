<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SymfonyApiClient extends BaseService
{
    private Client $client;
    private string $baseUrl;
    private string $username;
    private string $password;
    private ?string $token = null;

    private const URL = [
        'auth' => '/api/login',
        'getProducts' => '/api/admin/products'
    ];

    public function __construct()
    {
        $this->baseUrl = config('services.symfony.base_url');
        $this->username = config('services.symfony.auth_username');
        $this->password = config('services.symfony.auth_password');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30.0,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * @throws Exception
     */
    public function authenticate(): string
    {
        try {
            $result = $this->client->post(self::URL['auth'], [
                'json' => [
                    'username' => $this->username,
                    'password' => $this->password
                ]
            ]);

            $status = $result->getStatusCode();
            $body = $result->getBody()->getContents();
            $data = json_decode($body, true);

            $response = "✅ Успех! Статус: {$status}\n";

            if (isset($data['token'])) {
                $this->token = $data['token'];

                $response .= "🔑 Токен получен: " . substr($this->token, 0, 20) . "...\n";
                $response .= "📏 Длина токена: " . strlen($this->token) . " символов\n";
            } else {
                $response .= "📦 Ответ: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            return $response;
        } catch (Exception|GuzzleException $e) {
            return "❌ Ошибка: " . $e->getMessage();
        }
    }

    public function getProducts(): string
    {
        try {
            if (!$this->token) {
                return "❌ Ошибка: Токен не получен. Сначала выполните аутентификацию.";
            }

            $result = $this->client->get(self::URL['getProducts'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ]
            ]);

            $status = $result->getStatusCode();
            $body = $result->getBody()->getContents();
            $data = json_decode($body, true);

            return "✅ Продукты получены! Статус: {$status}\n" .
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (Exception|GuzzleException $e) {
            return "❌ Ошибка: " . $e->getMessage();
        }
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
}
