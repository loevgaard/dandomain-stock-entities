<?php

declare(strict_types = 1);

namespace Loevgaard\DandomainStock\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Loevgaard\DandomainStock\Entity\StockMovement;
use Loevgaard\DandomainStock\Repository\Generated\StockMovementRepositoryTrait;

class StockMovementRepository extends AbstractRepository
{
    use StockMovementRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockMovement::class);
    }
}
