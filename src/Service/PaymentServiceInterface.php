<?php
/**
 * Task service interface.
 */

namespace App\Service;

use App\Entity\Payment;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface PaymentServiceInterface.
 */
interface PaymentServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Payment $payment Payment entity
     */
    public function save(Payment $payment): void;
}
