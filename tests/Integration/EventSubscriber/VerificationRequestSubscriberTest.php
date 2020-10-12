<?php

namespace App\Tests\Integration\EventSubscriber;

use App\Entity\VerificationRequest;
use App\EventSubscriber\VerificationRequestSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Testing VerificationRequestSubscriber
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class VerificationRequestSubscriberTest extends KernelTestCase
{
    /**
     * @var TokenStorageInterface|MockObject
     */
    private ?MockObject $tokenStorageMock;

    /**
     * @var Security|MockObject
     */
    private ?MockObject $securityMock;

    /**
     * @var EntityManagerInterface|MockObject
     */
    private ?MockObject $entityManagerMock;

    /**
     * @var Swift_Mailer|MockObject
     */
    private ?MockObject $mailerMock;

    /**
     * @var ParameterBagInterface|MockObject
     */
    private ?MockObject $paramsMock;

    /**
     * @var VerificationRequestSubscriber
     */
    private VerificationRequestSubscriber $verificationRequestSubscriber;

    public function testEventSubscription()
    {
        $this->assertArrayHasKey(KernelEvents::VIEW, VerificationRequestSubscriber::getSubscribedEvents());
    }

    public function setUp(): void
    {
        $this->tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->securityMock = $this->getMockBuilder(Security::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailerMock = $this->getMockBuilder(Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->paramsMock = $this->getMockBuilder(ParameterBagInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->verificationRequestSubscriber = new VerificationRequestSubscriber(
            $this->tokenStorageMock,
            $this->securityMock,
            $this->entityManagerMock,
            $this->mailerMock,
            $this->paramsMock
        );

        self::bootKernel();
    }

    public function testProcessVerificationRequest()
    {
        $event = new ViewEvent(
            self::$kernel,
            $this->prepareRequest(),
            1,
            (new VerificationRequest())
        );

        $this->securityMock
            ->expects($this->once())
            ->method('isGranted')
            ->withConsecutive(['ROLE_ADMIN']);
        
        $this->verificationRequestSubscriber->processVerificationRequest($event);
    }

    private function prepareRequest(): Request
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_PATCH);

        return $request;
    }

    public function tearDown(): void
    {
        $this->tokenStorageMock = null;
        $this->securityMock = null;
        $this->entityManagerMock = null;
        $this->mailerMock = null;
        $this->paramsMock = null;
    }
}