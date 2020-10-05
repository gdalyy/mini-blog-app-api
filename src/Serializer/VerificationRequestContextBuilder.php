<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\VerificationRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Managing requests contexts.
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 * @see https://api-platform.com/docs/core/serialization/
 */
final class VerificationRequestContextBuilder implements SerializerContextBuilderInterface
{
    /**
     * @var SerializerContextBuilderInterface
     */
    private SerializerContextBuilderInterface $decorated;

    /**
     * @var AuthorizationCheckerInterface
     */
    private AuthorizationCheckerInterface$authorizationChecker;

    /**
     * VerificationRequestContextBuilder constructor.
     * @param SerializerContextBuilderInterface $decorated
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param Request $request
     * @param bool $normalization
     * @param array|null $extractedAttributes
     * @return array
     */
    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        if ($resourceClass === VerificationRequest::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_ADMIN') && false === $normalization) {
            $context['groups'][] = 'admin:input';
        }

        return $context;
    }
}