<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Aggregate;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\Model\Event\Link\LinkWasCreated;
use App\Core\Domain\Model\Event\Link\LinkWasDeleted;
use App\Core\Domain\Model\Event\Link\LinkWasUpdated;
use App\Core\Domain\Model\VO\Link\LinkId;
use App\Core\Domain\Model\VO\Link\LinkLabel;
use App\Core\Domain\Model\VO\Link\LinkUrl;
use App\Core\Domain\Model\VO\Project\ProjectId;
use DateTimeImmutable;

class Link extends AggregateRoot
{
    private function __construct(
        private LinkId $id,
        private readonly ProjectId $projectId,
        private LinkUrl $url,
        private ?LinkLabel $label,
        private readonly DateTimeImmutable $createdAt,
    ) {
    }

    public static function create(
        LinkId $id,
        ProjectId $projectId,
        LinkUrl $url,
        ?LinkLabel $label = null,
    ): self {
        $instance = new self($id, $projectId, $url, $label, new DateTimeImmutable());
        $instance->recordEvent(LinkWasCreated::from($instance));

        return $instance;
    }

    public function update(LinkUrl $url, ?LinkLabel $label): void
    {
        $this->url = $url;
        $this->label = $label;
        $this->recordEvent(LinkWasUpdated::from($this));
    }

    public function delete(): void
    {
        $this->recordEvent(LinkWasDeleted::from($this));
    }

    public function id(): LinkId
    {
        return $this->id;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function url(): LinkUrl
    {
        return $this->url;
    }

    public function label(): ?LinkLabel
    {
        return $this->label;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
