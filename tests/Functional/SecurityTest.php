<?php


namespace App\Tests\Functional;

use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Testing Security endpoints
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class SecurityTest extends BaseApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function testRegisterUser(): void
    {
        $response = static::createClient()->request('POST', '/api/register', [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'json' => [
                'email' => 'test@mail.com',
                'firstname' => 'Test',
                'lastname' => 'Test',
                'password' => 'password'
            ]
        ]);

        $content = $response->toArray();

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertRegExp('~^/api/users/(\d+)$~', $response->getHeaders()['content-location'][0]);
        $this->assertSame('test@mail.com', $content['email']);
        $this->assertSame('Test', $content['firstname']);
        $this->assertSame('Test', $content['lastname']);

    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testRegisterUserWithExistentEmail(): void
    {
        static::createClient()->request('POST', '/api/register', [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'json' => [
                'email' => 'test@mail.com',
                'firstname' => 'Test',
                'lastname' => 'Test',
                'password' => 'password'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testRegisterUserWithMissingParameters(): void
    {
        static::createClient()->request('POST', '/api/register', [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'json' => [
                'email' => '',
                'firstname' => '',
                'lastname' => '',
                'password' => ''
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticationToken(): void
    {
        $response = static::createClient()->request('POST', '/api/authentication_token', [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'json' => [
                'email' => 'user1@mail.com',
                'password' => '12345678'
            ]
        ]);

        $content = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertNotNull($content['user']);
        $this->assertRegExp('~^/api/users/(\d+)$~', $content['user']);
        $this->assertNotNull($content['token']);
        $this->assertNotNull($content['refresh_token']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testRefreshToken(): void
    {
        $refreshTokenString = $this->createRefreshToken();

        $response = static::createClient()->request('POST', '/api/refresh_authentication_token', [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'json' => [
                'refresh_token' => $refreshTokenString
            ]
        ]);

        $content = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertNotNull($content['token']);
        $this->assertNotNull($content['refresh_token']);
    }

    private function createRefreshToken()
    {
        static::bootKernel();

        $refreshTokenManager = self::$container->get('gesdinet.jwtrefreshtoken.refresh_token_manager');

        $refreshToken = $refreshTokenManager->create();

        $refreshToken->setUsername('user1@mail.com');
        $refreshToken->setRefreshToken($refreshTokenString = 'this-is-not-a-secure-refresh-token');
        $refreshToken->setValid((new DateTime())->modify(sprintf('+%d seconds', 60)));

        $refreshTokenManager->save($refreshToken);

        return $refreshTokenString;
    }
}