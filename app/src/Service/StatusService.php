<?php

namespace App\Service;

use App\Service\Contract\StatusInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class StatusService implements StatusInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Retrieves the related status from the database and assigns it to the entity.
     *
     * @template T of object
     * @param class-string<T> $statusClassName
     */
    public function setStatus(mixed $entity, string $statusClassName, string $statusConst): mixed
    {
        /** @var \Doctrine\ORM\EntityRepository<T> $statusRepository */
        $statusRepository = $this->entityManager->getRepository($statusClassName);
        $status = $statusRepository->findOneBy([
            'const' => $statusConst
        ]);

        if (!$status) {
            throw new InvalidArgumentException("The requested status does not exist.");
        }

        if (!method_exists($entity, 'setStatus')) {
            throw new InvalidArgumentException("The entity does not have a setStatus method.");
        }

        $entity->setStatus($status);
        return $entity;
    }
}
