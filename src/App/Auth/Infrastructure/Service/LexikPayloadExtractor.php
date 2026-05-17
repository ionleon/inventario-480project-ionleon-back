<?php

declare(strict_types=1);

namespace App\App\Auth\Infrastructure\Service;

use App\App\Auth\Domain\Service\TokenPayloadExtractorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class LexikPayloadExtractor implements TokenPayloadExtractorInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly RequestStack $requestStack,
    ) {}

    public function extractFromCurrentRequest(): ?array
    {
        $token = $this->tokenStorage->getToken();

        if ($token && method_exists($token, 'getPayload')) {
            $payload = $token->getPayload();
            return $this->formatPayload($payload);
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $authHeader = $request->headers->get('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $jwt = substr($authHeader, 7);
            try {
                return $this->formatPayload($this->jwtManager->parse($jwt));
            } catch (\Exception) {
                return null;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{jti: string, exp: int, ttl: int}|null
     */
    private function formatPayload(array $payload): ?array
    {
        if (!$payload || !isset($payload['jti'], $payload['exp'])) {
            return null;
        }

        $exp = (int) $payload['exp'];
        $ttl = $exp - time();

        return [
            'jti' => (string) $payload['jti'],
            'exp' => $exp,
            'ttl' => $ttl,
        ];
    }
}
