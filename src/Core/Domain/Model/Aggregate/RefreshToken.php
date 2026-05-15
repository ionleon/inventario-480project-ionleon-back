<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Model\Event\RefreshToken\RefreshTokenWasIssued;
use App\Core\Domain\Model\Event\RefreshToken\RefreshTokenWasRevoked;
use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenExpiresAt;
use App\Core\Domain\Model\VO\RefreshToken\RefreshTokenValue;
use DateTimeImmutable;
use DateTimeInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * RefreshToken aggregate.
 *
 * Implements the bundle's RefreshTokenInterface to keep the existing
 * /480project/token/refresh endpoint working. The interface forces setter
 * methods (setRefreshToken, setUsername, setValid) which violate the usual
 * "no setters" rule of aggregates — those setters exist solely for the
 * bundle's hydration paths. Internal mutations prefer the issue()/revoke()
 * business methods which record domain events.
 */
class RefreshToken extends AggregateRoot implements RefreshTokenInterface
{
    private ?int $id = null;
    private ?string $refreshToken = null;
    private ?string $username = null;
    private ?DateTimeInterface $valid = null;

    private function __construct(string $refreshToken, string $username, DateTimeInterface $valid)
    {
        $this->refreshToken = $refreshToken;
        $this->username = $username;
        $this->valid = $valid;
    }

    /**
     * Required by the bundle's RefreshTokenInterface.
     * Called by the bundle to create tokens on login success.
     */
    public static function createForUserWithTtl(string $refreshToken, UserInterface $user, int $ttl): static
    {
        $valid = new \DateTime();

        if ($ttl > 0) {
            $valid->modify('+' . $ttl . ' seconds');
        } elseif ($ttl < 0) {
            $valid->modify($ttl . ' seconds');
        }

        $instance = new static($refreshToken, $user->getUserIdentifier(), $valid);
        $instance->recordEvent(RefreshTokenWasIssued::from($instance));

        return $instance;
    }

    public static function issue(RefreshTokenValue $value, string $username, RefreshTokenExpiresAt $expiresAt): self
    {
        $instance = new self((string) $value, $username, $expiresAt->value());
        $instance->recordEvent(RefreshTokenWasIssued::from($instance));

        return $instance;
    }

    public function revoke(): void
    {
        $past = new DateTimeImmutable('-1 second');
        $this->valid = $past;
        $this->recordEvent(RefreshTokenWasRevoked::from($this));
    }

    // RefreshTokenInterface methods (the bundle's contract)

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getValid(): ?DateTimeInterface
    {
        return $this->valid;
    }

    public function setRefreshToken(?string $refreshToken = null): static
    {
        $this->refreshToken = (string) $refreshToken;

        return $this;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function setValid(DateTimeInterface $valid): static
    {
        $this->valid = $valid;

        return $this;
    }

    public function isValid(): bool
    {
        return null !== $this->valid && $this->valid >= new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return !in_array($this->refreshToken, [null, '', '0'], true) ? (string) $this->refreshToken : '';
    }
}
