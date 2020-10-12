<?php

namespace App\Tests\Functional;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Testing Post endpoints
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class PostTest extends BaseApiTestCase
{
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticatedGetCollection(): void
    {
        static::createAuthenticatedClient()->request('GET', '/api/posts');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Post::class);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testUnAuthenticatedGetCollection(): void
    {
        static::createClient()->request('GET', '/api/posts', [
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticatedUserPostCollection(): void
    {
        static::createAuthenticatedClient()->request('POST', '/api/posts', [
            'json' => [
                'title' => 'Test blog post',
                'content' => 'This is a test blog post'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticatedBloggerPostCollection(): void
    {
        $response = static::createAuthenticatedClient('blogger1@mail.com', '12345678')->request('POST', '/api/posts', [
            'json' => [
                'title' => 'Test blog post',
                'content' => 'This is a test blog post'
            ]
        ]);

        $content = $response->toArray();

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertRegExp('~^/api/posts/(\d+)$~', $response->getHeaders()['content-location'][0]);
        $this->assertNotNull($content['id']);
        $this->assertRegExp('~(\d+)~', $content['id']);
        $this->assertSame('Test blog post', $content['title']);
        $this->assertSame('This is a test blog post', $content['content']);
        $this->assertNotNull($content['user']);
        $this->assertRegExp('~^/api/users/(\d+)$~', $content['user']);
        $this->assertNotNull($content['date']);
        $this->assertMatchesResourceItemJsonSchema(Post::class);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticatedClientGetItem(): void
    {
        $response = static::createAuthenticatedClient()->request('GET', '/api/posts/1');

        $content = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertSame(1, $content['id']);
        $this->assertNotNull($content['title']);
        $this->assertNotNull($content['content']);
        $this->assertSame('/api/users/6', $content['user']);
        $this->assertNotNull($content['date']);
        $this->assertMatchesResourceItemJsonSchema(Post::class);
    }


    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticatedClientGetNonExistentItem(): void
    {
        static::createAuthenticatedClient()->request('GET', '/api/posts/xxx');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticatedBloggerPutItem(): void
    {
        $response = static::createAuthenticatedClient('blogger1@mail.com', '12345678')->request('PUT', '/api/posts/1', [
            'json' => [
                'title' => 'Test update blog post',
                'content' => 'This is an updated test blog post'
            ]
        ]);

        $content = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertSame(1, $content['id']);
        $this->assertSame('Test update blog post', $content['title']);
        $this->assertSame('This is an updated test blog post', $content['content']);
        $this->assertSame('/api/users/6', $content['user']);
        $this->assertNotNull($content['date']);
        $this->assertMatchesResourceItemJsonSchema(Post::class);
    }
}