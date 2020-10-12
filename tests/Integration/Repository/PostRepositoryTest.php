<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Testing PostRepository
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class PostRepositoryTest extends KernelTestCase
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

    public function testSearchByUser()
    {
        $result = $this->entityManager->getRepository(Post::class)->findBy(['user' => 7]);

        $this->assertCount(5, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}