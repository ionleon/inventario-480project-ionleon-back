<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Model\Event\Contact\ContactWasCreated;
use App\Core\Domain\Model\Event\Contact\ContactWasDeleted;
use App\Core\Domain\Model\Event\Contact\ContactWasMarkedAsMain;
use App\Core\Domain\Model\Event\Contact\ContactWasUpdated;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Common\Email;
use App\Core\Domain\Model\VO\Common\Phone;
use App\Core\Domain\Model\VO\Contact\ContactId;
use App\Core\Domain\Model\VO\Contact\ContactName;

class Contact extends AggregateRoot
{
    private function __construct(
        private ContactId $id,
        private readonly ClientId $clientId,
        private ContactName $fullName,
        private Email $email,
        private Phone $phoneNumber,
        private ?string $note,
        private bool $isMain,
    ) {}

    public static function create(
        ContactId $id,
        ClientId $clientId,
        ContactName $fullName,
        Email $email,
        Phone $phoneNumber,
        ?string $note = null,
        bool $isMain = false,
    ): self {
        $instance = new self(
            id: $id,
            clientId: $clientId,
            fullName: $fullName,
            email: $email,
            phoneNumber: $phoneNumber,
            note: $note,
            isMain: $isMain,
        );

        $instance->recordEvent(ContactWasCreated::from($instance));

        return $instance;
    }

    public function update(
        ContactName $fullName,
        Email $email,
        Phone $phoneNumber,
        ?string $note,
    ): void {
        $this->fullName = $fullName;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->note = $note;
        $this->recordEvent(ContactWasUpdated::from($this));
    }

    public function markAsMain(): void
    {
        if ($this->isMain) {
            return;
        }
        $this->isMain = true;
        $this->recordEvent(ContactWasMarkedAsMain::from($this));
    }

    public function unmarkAsMain(): void
    {
        $this->isMain = false;
    }

    public function delete(): void
    {
        $this->recordEvent(ContactWasDeleted::from($this));
    }

    public function id(): ContactId
    {
        return $this->id;
    }

    public function clientId(): ClientId
    {
        return $this->clientId;
    }

    public function fullName(): ContactName
    {
        return $this->fullName;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function phoneNumber(): Phone
    {
        return $this->phoneNumber;
    }

    public function note(): ?string
    {
        return $this->note;
    }

    public function isMain(): bool
    {
        return $this->isMain;
    }
}
