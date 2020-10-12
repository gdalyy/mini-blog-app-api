<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * BaseApiTestCase class
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class BaseApiTestCase extends ApiTestCase
{
    /**
     * Create a client with a default Authorization header.
     *
     * @param string $email
     * @param string $password
     *
     * @return Client
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    protected function createAuthenticatedClient($email = 'user1@mail.com', $password = '12345678')
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/authentication_token', [
                'json' => [
                    'email' => $email,
                    'password' => $password
                ]
            ]
        );

        $data = $response->toArray();

        $client = static::createClient([]);
        $client->setDefaultOptions([
            'auth_bearer' => "{$data['token']}",
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        return $client;
    }
}