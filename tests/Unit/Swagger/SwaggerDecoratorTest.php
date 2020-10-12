<?php

use ApiPlatform\Core\Documentation\Documentation;
use ApiPlatform\Core\Metadata\Resource\ResourceNameCollection;
use ApiPlatform\Core\Swagger\Serializer\DocumentationNormalizer;
use App\Entity\MediaObject;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\VerificationRequest;
use App\Swagger\SwaggerDecorator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Testing swagger doc decoration
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class SwaggerDecoratorTest extends TestCase
{
    const AUTHENTICATION_TOKEN_PATH = '/api/authentication_token';
    const REFRESH_AUTHENTICATION_TOKEN_PATH = '/api/refresh_authentication_token';
    /**
     * @var NormalizerInterface|MockObject
     */
    private ?MockObject $normalizer;
    /**
     * @var SwaggerDecorator
     */
    private SwaggerDecorator $swaggerDecorator;
    /**
     * @var ResourceNameCollection
     */
    private ResourceNameCollection $apiDocumentation;

    protected function setUp(): void
    {
        $this->normalizer = $this->getMockBuilder(NormalizerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->swaggerDecorator = new SwaggerDecorator(
            $this->normalizer
        );
    }

    /**
     * @dataProvider documentationProvider
     * @param $documentation
     * @throws ExceptionInterface
     */
    public function testNormalize($documentation)
    {
        $this->normalizer
            ->expects($this->once())
            ->method('normalize')
            ->withConsecutive([$documentation, DocumentationNormalizer::FORMAT]);

        $result = $this->swaggerDecorator->normalize($documentation, DocumentationNormalizer::FORMAT);

        $this->assertIsArray($result);

        $this->assertArrayHasKey(self::AUTHENTICATION_TOKEN_PATH, $result['paths']);
        $this->assertNotNull($result['paths'][self::AUTHENTICATION_TOKEN_PATH]);

        $this->assertArrayHasKey(self::REFRESH_AUTHENTICATION_TOKEN_PATH, $result['paths']);
        $this->assertNotNull($result['paths'][self::REFRESH_AUTHENTICATION_TOKEN_PATH]);
    }

    public function documentationProvider()
    {
        $documentation = new Documentation(
            new ResourceNameCollection([
                VerificationRequest::class,
                Post::class,
                User::class,
                MediaObject::class,
            ])
        );

        return [
            [$documentation]
        ];
    }

    protected function tearDown(): void
    {
        $this->normalizer = null;
    }
}