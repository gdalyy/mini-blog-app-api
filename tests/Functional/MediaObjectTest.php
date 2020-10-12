<?php

namespace App\Tests\Functional;

use App\Faker\Provider\Image;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * Testing MediaObject endpoints
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class MediaObjectTest extends WebTestCase
{
    public function testPostCollection(): void
    {
        $testImage = Image::image('/home/gdaly/', 640, 480, 'people', true, null, null, null, 'test-image.jpg');
        $this->assertFileExists($testImage);

        $uploadedFile = new UploadedFile(
            $testImage,
            'test-image.jpg',
            'image/jpeg',
            null,
            true
        );

        $client = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/api/media_objects',
            [],
            ['file' => $uploadedFile],
            ['HTTP_ACCEPT' => 'application/json'],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

        $this->assertFileDoesNotExist($testImage);
    }

    public function testPostCollectionWithMissingFile(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/api/media_objects',
            [],
            [],
            ['HTTP_ACCEPT' => 'application/json'],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
    }

    /**
     * Create a client with a default Authorization header.
     *
     * This method was introduced because of 'test.api_platform.client'
     * does not support file upload , instead using test.client to resolve problem.
     *
     * @param string $email
     * @param string $password
     *
     * @return KernelBrowser
     */
    private function createAuthenticatedClient($email = 'user1@mail.com', $password = '12345678')
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/authentication_token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => $password,
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        self::ensureKernelShutdown();

        $client = self::createClient([]);
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }
}