<?php

namespace App\Doctrine;

use DateTimeInterface;
use Doctrine\Persistence\ObjectManager;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManager as BaseRefreshTokenManager;

/**
 * Class RefreshTokenManager.
 *
 * @author Alexander <by.haskell@gmail.com>
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
class RefreshTokenManager extends BaseRefreshTokenManager
{
    /**
     * @var ObjectManager
     */
    protected ObjectManager $objectManager;

    /**
     * @var string
     */
    protected string $class;

    /**
     * @var RefreshTokenRepository
     */
    protected RefreshTokenRepository $repository;

    /**
     * RefreshTokenManager constructor.
     * @param ObjectManager $objectManager
     * @param $class
     */
    public function __construct(ObjectManager $objectManager, $class)
    {
        $this->objectManager = $objectManager;

        $repository = $objectManager->getRepository($class);
        assert($repository instanceof RefreshTokenRepository);
        $this->repository = $repository;

        $metadata = $objectManager->getClassMetadata($class);

        $classString = $metadata->getName();
        $this->class = $classString;
    }

    /**
     * @param string $refreshToken
     *
     * @return RefreshTokenInterface|null
     */
    public function get($refreshToken): ?RefreshTokenInterface
    {
        $refreshToken = $this->repository->findOneBy(['refreshToken' => $refreshToken]);
        if ($refreshToken instanceof RefreshTokenInterface) {
            return $refreshToken;
        } else {
            return null;
        }
    }

    /**
     * @param string $username
     *
     * @return RefreshTokenInterface
     */
    public function getLastFromUsername($username): ?RefreshTokenInterface
    {
        $refreshToken = $this->repository->findOneBy(['username' => $username], ['valid' => 'DESC']);

        if ($refreshToken instanceof RefreshTokenInterface) {
            return $refreshToken;
        } else {
            return null;
        }
    }

    /**
     * @param RefreshTokenInterface $refreshToken
     * @param bool|true $andFlush
     */
    public function save(RefreshTokenInterface $refreshToken, $andFlush = true): void
    {
        $this->objectManager->persist($refreshToken);

        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * @param RefreshTokenInterface $refreshToken
     * @param bool $andFlush
     */
    public function delete(RefreshTokenInterface $refreshToken, $andFlush = true): void
    {
        $this->objectManager->remove($refreshToken);

        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * @param DateTimeInterface|null $datetime
     * @param bool $andFlush
     *
     * @return RefreshTokenInterface[]
     */
    public function revokeAllInvalid(?DateTimeInterface $datetime = null, $andFlush = true)
    {
        $invalidTokens = $this->repository->findInvalid($datetime);

        foreach ($invalidTokens as $invalidToken) {
            $this->objectManager->remove($invalidToken);
        }

        if ($andFlush) {
            $this->objectManager->flush();
        }

        return $invalidTokens;
    }

    /**
     * Returns the RefreshToken fully qualified class name.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}