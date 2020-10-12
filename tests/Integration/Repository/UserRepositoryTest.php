<?php

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Testing UserRepository
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class UserRepositoryTest extends KernelTestCase
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

    public function testUpgradePassword()
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        /**@var User $user user2 */
        $user = $userRepository->find(2);
        $userRepository->upgradePassword($user, 'this-is-not-an-encoded-password');

        $this->assertSame($user->getPassword(), 'this-is-not-an-encoded-password');

        $user = new \Symfony\Component\Security\Core\User\User('test-username', 'test-password');
        $this->expectException(UnsupportedUserException::class);
        $userRepository->upgradePassword($user, 'this-is-not-an-encoded-password');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}