<?php

namespace App\EventListener;

use Gesdinet\JWTRefreshTokenBundle\EventListener\AttachRefreshTokenOnSuccessListener as BaseAttachRefreshTokenOnSuccessListener;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

/**
 * Attach refresh token listener to api_authentication & api_refresh_token payloads.
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class AttachRefreshTokenOnSuccessListener extends BaseAttachRefreshTokenOnSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function attachRefreshToken(AuthenticationSuccessEvent $event)
    {
        parent::attachRefreshToken($event);
    }
}
