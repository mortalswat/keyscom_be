<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\RollbackPermission;

use App\Application\Shared\Command\CommandHandlerInterface;
use App\Domain\User\Entity\ActionUserOnMachine;
use App\Domain\User\Enums\ActionOfUserOnMachine;
use App\Domain\User\Enums\PermissionType;
use App\Domain\User\Repository\ActionUserOnMachineRepositoryInterface;
use App\Domain\User\Repository\PermissionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;

class RollbackPermissionHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly PermissionRepositoryInterface $permissionRepository,
        private readonly ActionUserOnMachineRepositoryInterface $actionUserOnMachineRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LockFactory $lockFactory,
    ) {}

    public function __invoke(RollbackPermissionCommand $rollbackPermissionCommand)
    {
        $permission = $this->permissionRepository->getByUuid($rollbackPermissionCommand->permissionUuid) ??
            throw new \Exception('Not exist the permission');

        if (is_null($this->permissionRepository->getParentOrSamePermissionOfUser(
            $rollbackPermissionCommand->loggedUser,
            PermissionType::ADMIN,
            $permission->getRelatedEntity(),
            $permission->getTypeOfMachine(),
            $permission->getRelatedEntityUuid()
        ))) {
            throw new \Exception('You has not permissions for assign this');
        }

        $lock = $this->lockFactory->createLock($permission->getUuid());
        $lock->acquire(true);

        try {
            $this->entityManager->getConnection()->beginTransaction();

            $actions = $permission->getActions();
            foreach ($actions as $action) {
                if ($action->isProcessed()) {
                    $this->actionUserOnMachineRepository->save(new ActionUserOnMachine(
                        null,
                        $action->getPermission(),
                        $action->getMachine(),
                        $action->getActionToDo() === ActionOfUserOnMachine::ADD ?
                            ActionOfUserOnMachine::REMOVE :
                            ActionOfUserOnMachine::ADD
                    ));
                } else {
                    $action->setCanceled(true);
                    $this->actionUserOnMachineRepository->save($action);
                }
            }
            $permission->setReverted(true);
            $this->permissionRepository->save($permission);

            $this->entityManager->getConnection()->commit();

        } catch (\Throwable $exception) {
            $this->entityManager->getConnection()->rollBack();
            throw $exception;

        } finally {
            $lock->release();
        }
    }
}
