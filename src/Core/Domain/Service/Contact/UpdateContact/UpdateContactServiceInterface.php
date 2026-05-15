<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Contact\UpdateContact;

use App\Core\Domain\Model\Aggregate\Contact;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Core\Domain\Model\VO\Contact\ContactId;
use App\Core\Domain\Model\VO\Contact\ContactName;

interface UpdateContactServiceInterface
{
    public function __invoke(
        ContactId $id,
        ContactName $fullName,
        Email $email,
        Phone $phoneNumber,
        ?string $note,
    ): Contact;
}
