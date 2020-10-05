<?php

namespace App\EventListener;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

/**
 * Override LexikJWT authentication success response.
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class AuthenticationSuccessListener implements AuthenticationSuccessListenerInterface
{
    /**
     * @var IriConverterInterface
     */
    private IriConverterInterface $iriConverter;

    /**
     * AuthenticationSuccessListener constructor.
     * @param IriConverterInterface $iriConverter
     */
    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->iriConverter = $iriConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!($user instanceof User)) {
            return;
        }

        $data['user'] = $this->iriConverter->getIriFromItem($user);
        $data['token'] = $event->getData()['token'];
        $data['refresh_token'] = $event->getData()['refresh_token'];

        $event->setData($data);
    }
}