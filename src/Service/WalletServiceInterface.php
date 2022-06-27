<?php
/**
 * WALLET service interface.
 */

namespace App\Service;

use App\Entity\User;
use App\Entity\Wallet;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface WalletServiceInterface.
 */
interface WalletServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int  $page Page number
     * @param User $user witch user
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $user): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Wallet $wallet User entity
     */
    public function save(Wallet $wallet): void;

    /**
     * Can Category be deleted?
     *
     * @param Wallet $category Wallet entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Wallet $category): bool;
}
