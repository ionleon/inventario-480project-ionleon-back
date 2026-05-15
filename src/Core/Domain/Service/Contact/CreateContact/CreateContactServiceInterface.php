<?php

declare(strict_types=1);

namespace App\Core\Domain\Service\Contact\CreateContact;

use App\Core\Domain\Model\Aggregate\Contact;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Core\Domain\Model\VO\Contact\ContactId;
use App\Core\Domain\Model\VO\Contact\ContactName;

interface CreateContactServiceInterface
{
    public function __invoke(
        ContactId $id,
        ClientId $clientId,
        ContactName $fullName,
        Email $email,
        Phone $phoneNumber,
        ?string $note,
    ): Contact;
}
