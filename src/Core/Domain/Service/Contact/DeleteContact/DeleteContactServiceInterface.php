<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Contact\DeleteContact;

use App\Core\Domain\Model\VO\Contact\ContactId;

interface DeleteContactServiceInterface
{
    public function __invoke(ContactId $id): void;
}
