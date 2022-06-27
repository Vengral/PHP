<?php
/**
 * Payment service.
 */

namespace App\Service;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use App\Repository\TransactionRepository;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    private TransactionRepository $transactionRepository;
    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param PaymentRepository     $paymentRepository     Payment repository
     * @param TransactionRepository $transactionRepository options transactions
     * @param PaginatorInterface    $paginator             options Paginator
     */
    public function __construct(PaymentRepository $paymentRepository, TransactionRepository $transactionRepository, PaginatorInterface $paginator)
    {
        $this->transactionRepository = $transactionRepository;
        $this->paymentRepository = $paymentRepository;
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
                $this->paymentRepository->queryAll(),
                $page,
                PaymentRepository::PAGINATOR_ITEMS_PER_PAGE
            );
        }

        return $this->paginator->paginate(
            $this->paymentRepository->queryLikeName($name),
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

    /**
     * Can Payment be deleted?
     *
     * @param Payment $category Payment entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Payment $category): bool
    {
        try {
            $result = $this->transactionRepository->countByPayment($category);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
}
