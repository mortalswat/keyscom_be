<?php

declare(strict_types=1);

namespace App\Application\Shared\Dto\Client;

class ClientDto
{
    private ?string $uuid;
    private string $name;

    /**
     * ClientDto constructor.
     * @param string|null $uuid
     * @param string $name
     */
    public function __construct(?string $uuid, string $name)
    {
        $this->uuid = $uuid;
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}