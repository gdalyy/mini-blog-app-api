<?php

namespace App\DataFixtures;

use App\Entity\MediaObject;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\VerificationRequest;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Faker\Provider\Image;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Create refresh token fixtures.
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
class RefreshTokensFixtures extends Fixture
{
    /**
     * @var RefreshTokenManagerInterface
     */
    private RefreshTokenManagerInterface $refreshTokenManager;

    public function __construct(RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->refreshTokenManager = $refreshTokenManager;
    }

    public function load(ObjectManager $manager)
    {
        // insert dummy valid refresh tokens for user 1
        for ($i = 1; $i <= 5; $i++) {
            $refreshToken = $this->refreshTokenManager->create();

            $refreshToken->setUsername('user1@mail.com');
            $refreshToken->setRefreshToken("this-is-a-valid-refresh-token-{$i}");
            $refreshToken->setValid((new DateTime())->modify(sprintf('+%d seconds', 3600)));

            $this->refreshTokenManager->save($refreshToken);
        }

        // insert dummy expired refresh tokens for user 1
        for ($i = 1; $i <= 5; $i++) {
            $refreshToken = $this->refreshTokenManager->create();

            $refreshToken->setUsername('user1@mail.com');
            $refreshToken->setRefreshToken("this-is-an-expired-refresh-token-{$i}");
            $refreshToken->setValid((new DateTime())->modify(sprintf('-%d seconds', 3600)));

            $this->refreshTokenManager->save($refreshToken);
        }
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
