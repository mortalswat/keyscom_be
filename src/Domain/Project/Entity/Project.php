<?php

declare(strict_types=1);

namespace App\Domain\Project\Entity;

use App\Domain\Client\Entity\Client;
use App\Domain\Shared\Auditable\AuditableEntityTrait;
use App\Domain\Tenant\CertainTenant\TenantEntityTrait;

class Project
{
    use AuditableEntityTrait;
    use TenantEntityTrait;

    private ?string $uuid;
    private string $name;
    private ?\DateTime $startDate;
    private ?\DateTime $endDate;

    private Client $client;

    /**
     * Project constructor.
     * @param string|null $uuid
     * @param string $name
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @param Client $client
     */
    public function __construct(?string $uuid, string $name, ?\DateTime $startDate, ?\DateTime $endDate, Client $client)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->client = $client;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     */
    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime|null $startDate
     */
    public function setStartDate(?\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime|null $endDate
     */
    public function setEndDate(?\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
