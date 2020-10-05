<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Entity\VerificationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Process a verification request.
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
final class VerificationRequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var Swift_Mailer
     */
    private Swift_Mailer  $mailer;

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $params;

    /**
     * VerificationRequestSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @param Swift_Mailer $mailer
     * @param ParameterBagInterface $params
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        Security $security,
        EntityManagerInterface $entityManager,
        Swift_Mailer $mailer,
        ParameterBagInterface $params
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->params = $params;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['processVerificationRequest', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * Approve or decline verification request and send notifications emails.
     * @param ViewEvent $event
     */
    public function processVerificationRequest(ViewEvent $event): void
    {
        $verificationRequest = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$verificationRequest instanceof VerificationRequest || !in_array($method, [Request::METHOD_PUT, Request::METHOD_PATCH])) {
            return;
        }

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $uow = $this->entityManager->getUnitOfWork();
        $uow->computeChangeSets();
        $changeSet = $uow->getEntityChangeSet($verificationRequest);

        // make sure that field status has been updated
        if (!isset($changeSet['status'])) {
            return;
        }

        $user = $verificationRequest->getUser();

        if ($verificationRequest->getStatus() == VerificationRequest::STATUS_VERIFICATION_APPROVED) {

            // promote user to blogger
            $user->setRoles(['ROLE_BLOGGER']);
            $this->entityManager->flush();

            // send acceptance email
            // twig may be injected for a better templating
            $message = (new Swift_Message('Your verification request has been approved'))
                ->setFrom($this->params->get('mailer_user') ?? 'support@blogapp.com')
                ->setTo($user->getEmail())
                ->setBody("Welcome on board {$verificationRequest->getUser()->getFirstname()}. You are now a verified blogger. Enjoy your journey !");

            $this->mailer->send($message);
        } else {
            // demote user
            $user->setRoles([]);
            $this->entityManager->flush();

            $body = "Hello {$verificationRequest->getUser()->getFirstname()}, unfortunately we will not be moving forward with your application.";
            if ($verificationRequest->getRejectionReason()) {
                $body .= "<br><b>Reason:</b> {$verificationRequest->getRejectionReason()}";
            }

            // send rejection email
            $message = (new Swift_Message('Your verification request has been declined'))
                ->setFrom($this->params->get('mailer_user') ?? 'support@blogapp.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $body,
                    'text/html'
                );

            $this->mailer->send($message);
        }
    }
}