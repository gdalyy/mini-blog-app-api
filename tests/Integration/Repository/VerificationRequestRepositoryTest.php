<?php

namespace App\Tests\Integration\Repository;

use App\Entity\VerificationRequest;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Testing VerificationRequestRepository
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class VerificationRequestRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSearchByStatus()
    {
        $result = $this->entityManager->getRepository(VerificationRequest::class)->findBy(['status' => VerificationRequest::STATUS_VERIFICATION_REQUESTED]);

        $this->assertNotEmpty($result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}