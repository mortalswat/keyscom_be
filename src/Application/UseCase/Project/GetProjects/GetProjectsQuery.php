<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjects;

use App\Application\Shared\Query\QueryInterface;
use App\Domain\Shared\Entities\PaginationProperties;
use App\Domain\User\Entity\User;

class GetProjectsQuery implements QueryInterface
{
    public function __construct(
        readonly User $loggedUser,
        readonly PaginationProperties $paginationProperties,
        readonly array $filters,
        readonly array $embeds,
    ) {}
}
