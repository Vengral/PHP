<?php
/**
 * Transaction service.
 */

namespace App\Service;

use App\Entity\Enum\UserRole;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TransactionRepository;
use DateTimeImmutable;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class TransactionService.
 */
class TransactionService implements TransactionServiceInterface
{
    /**
     * Transaction repository.
     */
    private TransactionRepository $transactionRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param TransactionRepository $transactionRepository Transaction repository
     * @param PaginatorInterface    $paginator             Paginator
     */
    public function __construct(TransactionRepository $transactionRepository, PaginatorInterface $paginator)
    {
        $this->transactionRepository = $transactionRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     * @param User $author Author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author): PaginationInterface
    {
        if (in_array(UserRole::ROLE_ADMIN->value, $author->getRoles())) {
            return $this->paginator->paginate(
                $this->transactionRepository->queryAll(),
                $page,
                TransactionRepository::PAGINATOR_ITEMS_PER_PAGE
            );
        }

        return $this->paginator->paginate(
            $this->transactionRepository->queryByAuthor($author),
            $page,
            TransactionRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Transaction $transaction Transaction entity
     */
    public function save(Transaction $transaction): void
    {
        if (null === $transaction->getId()) {
            $transaction->setCreatedAt(new DateTimeImmutable());
        }
        $transaction->setUpdatedAt(new DateTimeImmutable());

        $this->transactionRepository->save($transaction);
    }

    /**
     * Delete entity.
     *
     * @param Transaction $transaction Transaction entity
     */
    public function delete(Transaction $transaction): void
    {
        $this->transactionRepository->delete($transaction);
    }
}
