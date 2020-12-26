<?php

declare(strict_types=1);

namespace App\Application\Shared\Mapper\Pagination;

use App\Application\Shared\Dto\Pagination\PaginationDto;
use App\Application\Shared\Dto\Worker\ResponseWorkerWithoutContractsDto;
use App\Application\Shared\Mapper\MapperInterface;
use App\Domain\Worker\Entity\Worker;

class PaginationMapper implements MapperInterface
{
    /**
     * @param array $results
     * @param int|null $totalItems
     * @return PaginationDto
     */
    public function map($results, $totalItems = null)
    {
        return new PaginationDto(
            $results,
            $totalItems ?? count($results)
        );
    }
}
