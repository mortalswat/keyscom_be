<?php

declare(strict_types=1);

namespace App\Application\UseCase\Machine\CreateMachine;

use App\Application\Shared\Command\CommandHandlerInterface;
use App\Application\Shared\Dto\Machine\MachineDto;
use App\Application\Shared\Mapper\Machine\MachineMapper;
use App\Domain\Machine\Entity\Machine;
use App\Domain\Machine\Repository\MachineRepositoryInterface;
use App\Domain\Project\Repository\ProjectRepositoryInterface;
use App\Domain\Shared\Errors\DomainError;
use App\Domain\User\Enums\PermissionType;

class CreateMachineHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly MachineRepositoryInterface $machineRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly MachineMapper $machineMapper,
    ) {}

    public function __invoke(CreateMachineCommand $createMachineCommand): MachineDto
    {
        $project = $this->projectRepository->getByUuid($createMachineCommand->projectUuid) ??
            throw new DomainError('Bad Project Uuid');

        $createMachineCommand->loggedUser->checkPermissionForProject($project, PermissionType::ADMIN);

        $machine = $this->machineRepository->save(new Machine(
            $createMachineCommand->uuid,
            $createMachineCommand->ip,
            $createMachineCommand->name,
            $createMachineCommand->domain,
            $createMachineCommand->type,
            $project,
        ));

        return $this->machineMapper->map($machine);
    }
}
