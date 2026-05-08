<?php

namespace App\Auth\Infrastructure\Service;

use App\Auth\Domain\Service\TokenPayloadExtractorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LexikPayloadExtractor implements TokenPayloadExtractorInterface
{

    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private JWTTokenManagerInterface $jwtManager,
        private RequestStack $requestStack,
    )
    {}

    /**
     * @inheritDoc
     */
    public function extractFromCurrentRequest(): ?array
    {
        $token = $this->tokenStorage->getToken();

        if ($token && method_exists($token, 'getPayload')) {
            $payload = $token->getPayload();
            return $this->formatPayload($payload);
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) return null;

        $authHeader = $this->requestStack->getCurrentRequest()->headers->get('Authorization');
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

    private function formatPayload(array $payload): ?array
    {
        if (!$payload || !isset($payload['jti'], $payload['exp'])) {
            return null;
        }

        $exp = (int) $payload['exp'];
        $currentTime = time();
        $ttl = $exp - $currentTime;

        return [
            'jti' => (string) $payload['jti'],
            'exp' => $exp,
            'ttl' => $ttl,
        ];
    }
}
