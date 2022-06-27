<?php
/**
 * Operation service.
 */

namespace App\Service;

use App\Entity\Operation;
use App\Repository\CategoryRepository;
use App\Repository\OperationRepository;
use App\Repository\TransactionRepository;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    private TransactionRepository $transactionRepository;

    /**
     * @param OperationRepository  options  $operationRepository
     * @param TransactionRepository options $transactionRepository
     * @param PaginatorInterface   options  $paginator
     */
    public function __construct(OperationRepository $operationRepository, TransactionRepository $transactionRepository, PaginatorInterface $paginator)
    {
        $this->transactionRepository = $transactionRepository;
        $this->operationRepository = $operationRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int         $page Page number
     * @param string|null $name options
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, ?string $name = null): PaginationInterface
    {
        if (null === $name) {
            return $this->paginator->paginate(
                $this->operationRepository->queryAll(),
                $page,
                CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
            );
        }

        return $this->paginator->paginate(
            $this->operationRepository->queryLikeName($name),
            $page,
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
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

    /**
     * Can Payment be deleted?
     *
     * @param Operation $category Operation entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Operation $category): bool
    {
        try {
            $result = $this->transactionRepository->countByOperation($category);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
}
