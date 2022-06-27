<?php
/**
 * Transaction repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Operation;
use App\Entity\Payment;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TransactionRepository.
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial transaction.{id, date, createdAt, updatedAt, name, amount}',
                'partial payment.{id, name}',
                'partial category.{id, name}',
                'partial wallet.{id, name}',
                'partial tags.{id, name}'
            )
            ->leftJoin('transaction.category', 'category')
            ->leftJoin('transaction.payment', 'payment')
            ->leftJoin('transaction.operation', 'operation')
            ->leftJoin('transaction.wallet', 'wallet')
            ->leftJoin('transaction.tags', 'tags')
            ->orderBy('transaction.updatedAt', 'DESC');
    }

    /**
     * Count transaction by category.
     *
     * @param Category $category Category
     *
     * @return int Number of transaction in category
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        try {
            return $qb->select($qb->expr()->countDistinct('transaction.id'))
                ->where('transaction.category = :category')
                ->setParameter(':category', $category)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return 999;
        }
    }

    /**
     * Count transaction by operation.
     *
     * @param Operation $operation Operation
     *
     * @return int Number of transaction in operation
     */
    public function countByOperation(Operation $operation): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        try {
            return $qb->select($qb->expr()->countDistinct('transaction.id'))
                ->where('transaction.operation = :operation')
                ->setParameter(':operation', $operation)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return 999;
        }
    }

    /**
     * Count transaction by payment.
     *
     * @param Payment $payment Payment
     *
     * @return int Number of transaction in payment
     */
    public function countByPayment(Payment $payment): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        try {
            return $qb->select($qb->expr()->countDistinct('transaction.id'))
                ->where('transaction.payment = :payment')
                ->setParameter(':payment', $payment)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return 999;
        }
    }

    /**
     * Count transaction by user.
     *
     * @param User $user User
     *
     * @return int Number of transaction in user
     */
    public function countByUser(User $user): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        try {
            return $qb->select($qb->expr()->countDistinct('transaction.id'))
                ->where('transaction.author = :author')
                ->setParameter(':author', $user)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return 999;
        }
    }

    /**
     * Save entity.
     *
     * @param Transaction $transaction Transaction entity
     */
    public function save(Transaction $transaction): void
    {
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Transaction $transaction Transaction entity
     */
    public function delete(Transaction $transaction): void
    {
        $this->_em->remove($transaction);
        $this->_em->flush();
    }

    /**
     * Query transactions by author.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(User $user): QueryBuilder
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->andWhere('transaction.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('transaction');
    }
}
