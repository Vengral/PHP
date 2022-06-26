<?php
/**
 * Payment service.
 */

namespace App\Service;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use DateTimeImmutable;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PaymentService.
 */
class PaymentService implements PaymentServiceInterface
{
    /**
     * Payment repository.
     */
    private PaymentRepository $paymentRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param PaymentRepository  $paymentRepository Payment repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(PaymentRepository $paymentRepository, PaginatorInterface $paginator)
    {
        $this->paymentRepository = $paymentRepository;
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
            $this->paymentRepository->queryAll(),
            $page,
            PaymentRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Payment $payment Payment entity
     */
    public function save(Payment $payment): void
    {
        if (null === $payment->getId()) {
            $payment->setCreatedAt(new DateTimeImmutable());
        }
        $payment->setUpdatedAt(new DateTimeImmutable());

        $this->paymentRepository->save($payment);
    }

    /**
     * Delete category.
     *
     * @param Payment $payment Payment entity
     */
    public function delete(Payment $payment): void
    {
        $this->paymentRepository->delete($payment);
    }
}
