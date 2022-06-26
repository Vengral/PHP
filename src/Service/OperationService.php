<?php
/**
 * Operation service.
 */

namespace App\Service;

use App\Entity\Operation;
use App\Repository\OperationRepository;
use DateTimeImmutable;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class OperationService.
 */
class OperationService implements OperationServiceInterface
{
    /**
     * Operation repository.
     */
    private OperationRepository $operationRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param OperationRepository $operationRepository Operation repository
     * @param PaginatorInterface  $paginator           Paginator
     */
    public function __construct(OperationRepository $operationRepository, PaginatorInterface $paginator)
    {
        $this->operationRepository = $operationRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->operationRepository->queryAll(),
            $page,
            OperationRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Operation $operation Operation entity
     */
    public function save(Operation $operation): void
    {
        if (null === $operation->getId()) {
            $operation->setCreatedAt(new DateTimeImmutable());
        }
        $operation->setUpdatedAt(new DateTimeImmutable());

        $this->operationRepository->save($operation);
    }

    /**
     * Delete operation.
     *
     * @param Operation $operation Operation entity
     */
    public function delete(Operation $operation): void
    {
        $this->operationRepository->delete($operation);
    }
}