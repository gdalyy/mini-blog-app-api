<?php

namespace App\Swagger;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Decorate swagger docs.
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 * @see https://api-platform.com/docs/core/swagger/
 */
final class SwaggerDecorator implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private NormalizerInterface $decorated;

    /**
     * SwaggerDecorator constructor.
     * @param NormalizerInterface $decorated
     */
    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    /**
     * {@inheritDoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $docs['components']['schemas']['AuthenticationSuccessPayload'] = [
            'type' => 'object',
            'properties' => [
                'user' => [
                    'type' => 'string',
                    'example' => '/api/users/1',
                ],
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $docs['components']['schemas']['Token'] = [
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $docs['components']['schemas']['RefreshToken'] = [
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
            ],
        ];

        $docs['components']['schemas']['Credentials'] = [
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'test@mail.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'test1234',
                ],
            ],
        ];

        $tokenDocumentation = [
            'paths' => [
                '/api/authentication_token' => [
                    'post' => [
                        'tags' => ['Security'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Get JWT token to login',
                        'requestBody' => [
                            'description' => 'Create new JWT Token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Credentials',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get JWT token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/AuthenticationSuccessPayload',
                                        ],
                                    ],
                                ],
                            ],
                            Response::HTTP_UNAUTHORIZED => [
                                'description' => 'Invalid credentials',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $refreshTokenDocumentation = [
            'paths' => [
                '/api/refresh_authentication_token' => [
                    'post' => [
                        'tags' => ['Security'],
                        'operationId' => 'postTokenItem',
                        'summary' => 'Refresh JWT token',
                        'requestBody' => [
                            'description' => 'Create new JWT Token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/RefreshToken',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Refresh Token Success',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Token',
                                        ],
                                    ],
                                ],
                            ],
                            Response::HTTP_UNAUTHORIZED => [
                                'description' => 'An authentication exception occurred',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $docs['paths']['/api/verification_requests']['post']['responses'][Response::HTTP_FORBIDDEN] = [
            'description' => 'This user already has a verification request',
        ];

        $docs['paths']['/api/verification_requests/{id}']['put']['requestBody']['description'] =
        $docs['paths']['/api/verification_requests/{id}']['patch']['requestBody']['description'] =
            "Fields **status** (VERIFICATION_APPROVED/VERIFICATION_DECLINED) and **rejectionReason** (string) can be added to payload when admin context";

        return array_merge_recursive($docs, $tokenDocumentation, $refreshTokenDocumentation);
    }
}