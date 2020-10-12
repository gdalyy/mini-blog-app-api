<?php

namespace App\Tests\Integration\Doctrine;

use App\Doctrine\RefreshTokenManager;
use Doctrine\ORM\EntityManager;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Testing RefreshTokenManager
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class RefreshTokenManagerTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private ?EntityManager $entityManager;

    /**
     * @var RefreshTokenManager
     */
    private ?RefreshTokenManager $refreshTokenManger;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->refreshTokenManger = $kernel->getContainer()
            ->get('gesdinet.jwtrefreshtoken.refresh_token_manager');
    }

    public function testGetLastFromUsername()
    {
        $refreshToken = $this->refreshTokenManger->getLastFromUsername('user1@mail.com');

        $this->assertNotNull($refreshToken);
    }

    public function testDeleteRefreshToken()
    {
        /** @var RefreshTokenInterface $refreshToken */
        $refreshToken = $this->entityManager->getRepository(RefreshToken::class)->find(2);
        $this->assertNotNull($refreshToken);

        $this->refreshTokenManger->delete($refreshToken);

        $refreshToken = $this->entityManager->getRepository(RefreshToken::class)->find(2);
        $this->assertNull($refreshToken);
    }

    public function testRevokeAllInvalid()
    {
        $invalidRefreshTokens = $this->refreshTokenManger->revokeAllInvalid(new \DateTime());

        $this->assertCount(5, $invalidRefreshTokens);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
        $this->refreshTokenManger = null;
    }
}