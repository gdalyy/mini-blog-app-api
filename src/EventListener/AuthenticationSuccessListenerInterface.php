<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

/**
 * Authentication success listener interface.
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
interface AuthenticationSuccessListenerInterface
{
    /**
     * Authentication Success
     *
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void;
}