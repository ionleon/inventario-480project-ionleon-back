<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Model\Event\Client\ClientWasActivated;
use App\Core\Domain\Model\Event\Client\ClientWasCreated;
use App\Core\Domain\Model\Event\Client\ClientWasDeactivated;
use App\Core\Domain\Model\Event\Client\ClientWasUpdated;
use App\Core\Domain\Model\VO\Client\ClientId;
use App\Core\Domain\Model\VO\Client\ClientName;
use App\Core\Domain\Model\VO\Sector\SectorId;

class Client extends AggregateRoot
{
    private function __construct(
        private readonly ClientId $id,
        private ClientName $name,
        private SectorId $sectorId,
        private bool $isActive,
    ) {}

    public static function create(
        ClientId $id,
        ClientName $name,
        SectorId $sectorId,
    ): self {
        $instance = new self(
            id: $id,
            name: $name,
            sectorId: $sectorId,
            isActive: true,
        );

        $instance->recordEvent(ClientWasCreated::from($instance));

        return $instance;
    }

    public function id(): ClientId
    {
        return $this->id;
    }

    public function name(): ClientName
    {
        return $this->name;
    }

    public function sectorId(): SectorId
    {
        return $this->sectorId;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function update(ClientName $name, SectorId $sectorId): void
    {
        $this->name = $name;
        $this->sectorId = $sectorId;
        $this->recordEvent(ClientWasUpdated::from($this));
    }

    public function activate(): void
    {
        if ($this->isActive) {
            return;
        }
        $this->isActive = true;
        $this->recordEvent(ClientWasActivated::from($this));
    }

    public function deactivate(): void
    {
        if (!$this->isActive) {
            return;
        }
        $this->isActive = false;
        $this->recordEvent(ClientWasDeactivated::from($this));
    }

    public function toggleActivation(): void
    {
        if ($this->isActive) {
            $this->deactivate();
        } else {
            $this->activate();
        }
    }
}
