<?php

declare(strict_types=1);

namespace App\Application\UseCase\Client\GetClients;

use App\Application\Shared\Dto\Pagination\PaginationDto;
use App\Application\Shared\Mapper\Client\ClientMapper;
use App\Application\Shared\Mapper\Pagination\PaginationMapper;
use App\Application\Shared\Query\QueryHandlerInterface;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Shared\Exception\ForbiddenException;
use App\Domain\User\Enums\PermissionRelatedEntity;

class GetClientsHandler implements QueryHandlerInterface
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private PaginationMapper $paginationMapper,
        private ClientMapper $clientMapper,
    ) {}

    public function __invoke(GetClientsQuery $getClientsQuery): PaginationDto
    {
        $filterFieldByRelatedEntityType = [
            PermissionRelatedEntity::CLIENT->name => 'uuid',
            PermissionRelatedEntity::PROJECT->name => 'projects.uuid',
            PermissionRelatedEntity::MACHINE->name => 'projects.machines.uuid',
        ];

        $filtersByPermissions = [];

        if(!$getClientsQuery->loggedUser->isSuper()) {
            $permissionsByRelatedEntity = $getClientsQuery->loggedUser->getPermissionsByRelatedEntity();
            foreach ($permissionsByRelatedEntity as $relatedEntity => $uuids) {
                foreach ($uuids as $uuid) {
                    if (!isset($filtersByPermissions[$filterFieldByRelatedEntityType[$relatedEntity]])) {
                        $filtersByPermissions[$filterFieldByRelatedEntityType[$relatedEntity]] = [];
                    }
                    $filtersByPermissions[$filterFieldByRelatedEntityType[$relatedEntity]][] = $uuid;
                }
            }

            if (empty($filtersByPermissions)) {
                throw new ForbiddenException();
            }
        }

        $results = $this->clientRepository->complexFind(
            $getClientsQuery->paginationProperties,
            $getClientsQuery->embeds,
            $getClientsQuery->filters,
            $filtersByPermissions,
        );

        return $this->paginationMapper->map(
            $this->clientMapper->mapArray($results, $getClientsQuery->embeds),
            $this->clientRepository->countAll($getClientsQuery->filters, $filtersByPermissions)
        );
    }
}
