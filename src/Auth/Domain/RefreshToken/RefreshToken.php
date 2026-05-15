<?php

namespace App\Auth\Domain\RefreshToken;

// MIGRATED: This entity has been replaced by App\Core\Domain\Model\Aggregate\RefreshToken.
// The #[ORM\Entity] attribute was removed to avoid duplicate mapping of the refresh_tokens table.
// This file is kept for historical reference only — do not use this class.

use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

class RefreshToken extends BaseRefreshToken
{
}
