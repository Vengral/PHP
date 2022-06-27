<?php
/**
 * Wallet service.
 */

namespace App\Service;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\TransactionRepository;
use App\Repository\WalletRepository;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class WalletService.
 */
class WalletService implements WalletServiceInterface
{
    /**
     * Wallet repository.
     */
    private WalletRepository $walletRepository;

    /**
     * transaction repository.
     */
    private TransactionRepository $transactionRepository;
    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param WalletRepository      $walletRepository      Wallet repository
     * @param TransactionRepository $transactionRepository options
     * @param PaginatorInterface    $paginator             Paginator
     */
    public function __construct(WalletRepository $walletRepository, TransactionRepository $transactionRepository, PaginatorInterface $paginator)
    {
        $this->walletRepository = $walletRepository;
        $this->transactionRepository = $transactionRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int  $page Page number
     * @param User $user options user
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $user): PaginationInterface
    {
        if (in_array(UserRole::ROLE_ADMIN->value, $user->getRoles())) {
            return $this->paginator->paginate(
                $this->walletRepository->queryAll(),
                $page,
                TransactionRepository::PAGINATOR_ITEMS_PER_PAGE
            );
        }

        return $this->paginator->paginate(
            $this->walletRepository->queryByAuthor($user),
            $page,
            TransactionRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Wallet $wallet Wallet entity
     */
    public function save(Wallet $wallet): void
    {
        if (null === $wallet->getId()) {
            $wallet->setCreatedAt(new DateTimeImmutable());
        }
        $wallet->setUpdatedAt(new DateTimeImmutable());

        $this->walletRepository->save($wallet);
    }

    /**
     * Can wallet be deleted?
     *
     * @param Wallet $category Wallet entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Wallet $category): bool
    {
        try {
            $result = $this->transactionRepository->countByWallet($category);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }

    /**
     * Delete category.
     *
     * @param Wallet $category Category entity
     */
    public function delete(Wallet $category): void
    {
        $this->walletRepository->delete($category);
    }
}
