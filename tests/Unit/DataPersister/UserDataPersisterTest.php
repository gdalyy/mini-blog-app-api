<?php

use App\DataPersister\UserDataPersister;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;
use Symfony\Component\Security\Core\Encoder\SodiumPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Testing User data persister
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class UserDataPersisterTest extends TestCase
{
    /**
     * @var EntityManagerInterface|MockObject
     */
    private ?MockObject $entityManagerMock;

    /**
     * @var UserDataPersister
     */
    private UserDataPersister $userDataPersister;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userDataPersister = new UserDataPersister(
            $this->entityManagerMock,
            new UserPasswordEncoder(
                new EncoderFactory([
                    User::class => new NativePasswordEncoder(),
                ])
            )
        );
    }


    /**
     * @dataProvider userProvider
     * @param User $user
     */
    public function testPersist(User $user)
    {
        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->withConsecutive([$user]);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->userDataPersister->persist($user);

        $this->assertNull($user->getPlainPassword());
    }

    /**
     * @dataProvider userProvider
     * @param User $user
     */
    public function testRemove(User $user)
    {
        $this->entityManagerMock
            ->expects($this->once())
            ->method('remove')
            ->withConsecutive([$user]);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->userDataPersister->remove($user);
    }

    /**
     * @test
     * @dataProvider userProvider
     * @param User $user
     */
    public function testSupports(User $user)
    {
        $this->assertTrue($this->userDataPersister->supports($user));
    }

    public function userProvider()
    {
        $user = new User();
        $user
            ->setEmail('test@mail.com')
            ->setFirstname('testFistname')
            ->setLastname('testLastname')
            ->setPlainPassword('aaaaaaaa');

        return [
            [$user]
        ];
    }

    protected function tearDown(): void
    {
        $this->entityManagerMock = null;
    }
}