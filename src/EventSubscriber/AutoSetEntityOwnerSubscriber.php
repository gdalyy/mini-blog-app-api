<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\VerificationRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Setting entities owners.
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
final class AutoSetEntityOwnerSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    /**
     * AutoSetEntityOwnerSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['autoSetOwner', EventPriorities::PRE_WRITE],
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function autoSetOwner(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!($entity instanceof Post || $entity instanceof VerificationRequest) || Request::METHOD_POST !== $method) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        $owner = $token->getUser();
        if (!$owner instanceof User) {
            return;
        }

        // checking one user per verification request constraint
        if ($entity instanceof VerificationRequest && $owner->getVerificationRequest()) {
            throw new AccessDeniedHttpException('This user already has a verification request');
        }

        $entity->setUser($owner);
    }
}