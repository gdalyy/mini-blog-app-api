<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\User;
use App\Faker\Provider\Image;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behatch\Context\RestContext;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of base api testing steps
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
final class ApiBaseContext implements Context
{
    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var JWTTokenManagerInterface
     */
    private JWTTokenManagerInterface $jwtManager;

    /**
     * @var RefreshTokenManagerInterface
     */
    private RefreshTokenManagerInterface $refreshTokenManager;

    /**
     * @var RestContext
     */
    private RestContext $restContext;

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;


    /**
     * @var User|null
     */
    private ?User $user;

    public function __construct(
        KernelInterface $kernel,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $jwtManager,
        RefreshTokenManagerInterface $refreshTokenManager,
        ParameterBagInterface $parameterBag
    ){
        $this->kernel = $kernel;
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @BeforeScenario @createDummyIDPhoto
     *
     * @param BeforeScenarioScope $scope
     */
    public function createDummyIDPhoto(BeforeScenarioScope $scope)
    {
        if (!file_exists($dummyDir = $this->parameterBag->get('kernel.project_dir') . '/features/dummy')) {
            mkdir($dummyDir);
        }

        Image::image($dummyDir, 640, 480, 'people', false, true, null, false, 'dummy-ID.jpg');
    }

    /**
     * @AfterScenario @deleteDummyIDPhoto
     */
    public function deleteDummyIDPhoto()
    {
        if (file_exists($dummyID = $this->parameterBag->get('kernel.project_dir') . '/features/dummy/dummy-ID.jpg')) {
            unlink($dummyID);
        }
    }

    /**
     * @BeforeScenario @loginAsAdmin
     *
     * @param BeforeScenarioScope $scope
     */
    public function loginAsAdmin(BeforeScenarioScope $scope)
    {
        $admin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@mail.com']);

        $token = $this->jwtManager->create($admin);

        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);
        $this->restContext->iAddHeaderEqualTo('Authorization', "Bearer $token");
    }

    /**
     * @BeforeScenario @loginAsUser
     *
     * @param BeforeScenarioScope $scope
     */
    public function loginAsUser(BeforeScenarioScope $scope)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user1@mail.com']);

        $token = $this->jwtManager->create($user);

        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);
        $this->restContext->iAddHeaderEqualTo('Authorization', "Bearer $token");
    }

    /**
     * @BeforeScenario @loginAsUserWithNoVerificationRequest
     *
     * @param BeforeScenarioScope $scope
     */
    public function loginAsUserWithNoVerificationRequest(BeforeScenarioScope $scope)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user-with-no-verification-request@mail.com']);

        $token = $this->jwtManager->create($user);

        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);
        $this->restContext->iAddHeaderEqualTo('Authorization', "Bearer $token");
    }

    /**
     * @BeforeScenario @loginAsBlogger
     *
     * @param BeforeScenarioScope $scope
     */
    public function loginAsBlogger(BeforeScenarioScope $scope)
    {
        $blogger = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'blogger1@mail.com']);

        $token = $this->jwtManager->create($blogger);

        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);
        $this->restContext->iAddHeaderEqualTo('Authorization', "Bearer $token");
    }

    /**
     * @AfterScenario @logout
     */
    public function logout()
    {
        $this->restContext->iAddHeaderEqualTo('Authorization', '');
    }

    /**
     * @Given /^am user with id (\d+)$/
     * @param int $userId
     */
    public function amUserWithId(int $userId)
    {
        $this->user = $this->entityManager->getRepository(User::class)->find($userId);
    }

    /**
     * @Given /^My refresh token is "([^"]*)"$/
     * @param string $refreshTokenString
     */
    public function myRefreshTokenIs(string $refreshTokenString)
    {
        $refreshToken = $this->refreshTokenManager->create();

        $refreshToken->setUsername($this->user->getUsername());
        $refreshToken->setRefreshToken($refreshTokenString);
        $refreshToken->setValid((new \DateTime())->modify(sprintf('+%d seconds', 60)));

        $this->refreshTokenManager->save($refreshToken);
    }
}
