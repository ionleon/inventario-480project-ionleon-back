<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Contact\MarkContactAsMain;

use App\Core\Domain\Model\VO\Contact\ContactId;

interface MarkContactAsMainServiceInterface
{
    public function __invoke(ContactId $id): void;
}
