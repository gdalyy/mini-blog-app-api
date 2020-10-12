<?php


namespace App\Tests\Functional;

use App\Entity\VerificationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Testing VerificationRequest endpoints
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class VerificationRequestTest extends BaseApiTestCase
{
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticatedUserPostCollection(): void
    {
        $response = static::createAuthenticatedClient('user-with-no-verification-request@mail.com', '12345678')
            ->request('POST', '/api/verification_requests', [
                'json' => [
                    'message' => 'I want to start my verification request process',
                    'image' => '/api/media_objects/11'
                ]
            ]);

        $content = $response->toArray();

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertRegExp('~^/api/verification_requests/(\d+)$~', $response->getHeaders()['content-location'][0]);
        $this->assertNotNull($content['id']);
        $this->assertRegExp('~(\d+)~', $content['id']);
        $this->assertSame(VerificationRequest::STATUS_VERIFICATION_REQUESTED, $content['status']);
        $this->assertNotNull($content['date']);
        $this->assertSame('I want to start my verification request process', $content['message']);
        $this->assertSame('/api/media_objects/11', $content['image']);
        $this->assertNotNull($content['user']);
        $this->assertRegExp('~^/api/users/(\d+)$~', $content['user']);
        $this->assertMatchesResourceItemJsonSchema(VerificationRequest::class);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticatedAdminGetCollection(): void
    {
        static::createAuthenticatedClient('admin@mail.com', '12345678')->request('GET', '/api/verification_requests');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(VerificationRequest::class);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticatedAdminPatchCollectionApprovedStatus(): void
    {
        $response = static::createAuthenticatedClient('admin@mail.com', '12345678')->request('PATCH', '/api/verification_requests/2', [
            'headers' => [
                'content-type' => 'application/merge-patch+json'
            ],
            'json' => [
                'status' => 'VERIFICATION_APPROVED'
            ]
        ]);

        $content = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertRegExp('~^/api/verification_requests/(\d+)$~', $response->getHeaders()['content-location'][0]);
        $this->assertSame(VerificationRequest::STATUS_VERIFICATION_APPROVED, $content['status']);
        $this->assertMatchesResourceItemJsonSchema(VerificationRequest::class);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAuthenticatedAdminPatchCollectionDeclinedStatus(): void
    {
        $response = static::createAuthenticatedClient('admin@mail.com', '12345678')->request('PATCH', '/api/verification_requests/2', [
            'headers' => [
                'content-type' => 'application/merge-patch+json'
            ],
            'json' => [
                'status' => 'VERIFICATION_DECLINED',
                'rejectionReason' => 'You will not be a blogger!'
            ]
        ]);

        $content = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertRegExp('~^/api/verification_requests/(\d+)$~', $response->getHeaders()['content-location'][0]);
        $this->assertSame(VerificationRequest::STATUS_VERIFICATION_DECLINED, $content['status']);
        $this->assertMatchesResourceItemJsonSchema(VerificationRequest::class);
    }
}