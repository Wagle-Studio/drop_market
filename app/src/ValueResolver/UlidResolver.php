<?php

namespace App\ValueResolver;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Resolves entity ulid route parameter.
 */
#[AsTargetedValueResolver("ulid_resolver")]
class UlidResolver implements ValueResolverInterface
{
    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
    }

    /**
     * @return array<mixed>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        $entityClass = $argument->getType();
        $ulid = $request->attributes->get($argument->getName() . "_ulid");

        if (!$entityClass || !$ulid) {
            return [];
        }

        if (!class_exists($entityClass)) {
            return [];
        }

        $repository = $this->entityManagerInterface->getRepository($entityClass);
        $entity = $repository->findOneBy(["ulid" => $ulid]);

        if (!$entity) {
            throw new NotFoundHttpException("The requested page does not exist.");
        }

        return [$entity];
    }
}
