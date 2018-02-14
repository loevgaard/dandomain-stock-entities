<?php

namespace Loevgaard\DandomainStock\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Loevgaard\DandomainFoundation\Repository\Generated\AbstractRepositoryTrait;

abstract class AbstractRepository extends ServiceEntityRepository
{
    use AbstractRepositoryTrait;

    /**
     * @var array
     */
    protected $options;

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        $this->options = [];

        parent::__construct($registry, $entityClass);
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\ORMException
     */
    public function persist($object) : void
    {
        $this->getEntityManager()->persist($object);
    }

    /**
     * @param null|object|array $entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flush($entity = null) : void
    {
        $this->getEntityManager()->flush($entity);
    }

    /**
     * Helper method for calling persist and flush successively
     *
     * @param object $entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($entity)
    {
        $this->persist($entity);
        $this->flush();
    }
}
