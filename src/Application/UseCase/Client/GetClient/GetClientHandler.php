<?php

declare(strict_types=1);

namespace App\Application\UseCase\Client\GetClient;

use App\Application\Shared\Dto\Client\ClientDto;
use App\Application\Shared\Mapper\Client\ClientMapper;
use App\Application\Shared\Query\QueryHandlerInterface;
use App\Domain\Client\Repository\ClientRepositoryInterface;

class GetClientHandler implements QueryHandlerInterface
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private ClientMapper $clientMapper,
    ) {}

    public function __invoke(GetClientQuery $getClientQuery): ClientDto
    {
        $client = $this->clientRepository->getByUuid(
            $getClientQuery->uuid,
            $getClientQuery->embeds,
        ) ?? throw new \Exception('Bad Client Uuid');

        $getClientQuery->loggedUser->checkPermissionForClient($client);

        return $this->clientMapper->map($client, $getClientQuery->embeds);
    }
}
