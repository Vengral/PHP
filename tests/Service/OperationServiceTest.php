<?php
/**
 * operationService tests.
 */

namespace App\Tests\Service;

use App\Entity\Operation;
use App\Repository\OperationRepository;
use App\Repository\TransactionRepository;
use App\Service\OperationService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class OperationServiceTest.
 */
class OperationServiceTest extends KernelTestCase
{
    /**
     * Operation service.
     *
     * @var OperationService|object|null
     */
    private ?OperationService $operationService;

    /**
     * Operation repository.
     *
     * @var OperationRepository|object|null
     */
    private ?OperationRepository $operationRepository;

    /**
     * Transaction repository.
     *
     * @var TransactionRepository|object|null
     */
    private ?TransactionRepository $transactionRepository;

    /**
     * Test save.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testSave(): void
    {
        // given
        $expectedOperation = new Operation();
        $expectedOperation->setName('Test Operation');
        $expectedOperation->setCreatedAt(new \DateTime('now'));
        $expectedOperation->setUpdatedAt(new \DateTime('now'));

        // when
        $this->operationService->save($expectedOperation);
        $resultOperation = $this->operationRepository->findOneById(
            $expectedOperation->getId()
        );

        // then
        $this->assertEquals($expectedOperation, $resultOperation);
    }

    /**
     * Test delete.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testDelete(): void
    {
        // given
        $expectedOperation = new Operation();
        $expectedOperation->setName('Test Operation');
        $expectedOperation->setCreatedAt(new \DateTime('now'));
        $expectedOperation->setUpdatedAt(new \DateTime('now'));
        $this->operationRepository->save($expectedOperation);
        $expectedId = $expectedOperation->getId();

        // when
        $this->operationService->delete($expectedOperation);
        $result = $this->operationRepository->findOneById($expectedId);

        // then
        $this->assertNull($result);
    }

    /**
     * Test pagination empty list.
     */
    public function testCreatePaginatedListEmptyList(): void
    {
        // given
        $page = 1;
        $dataSetSize = 0;
        $expectedResultSize = 0;

        // when
        $result = $this->operationService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Set up test.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;
        $this->operationRepository = $container->get(OperationRepository::class);
        $this->operationService = $container->get(OperationService::class);
        $this->transactionRepository = $container->get(TransactionRepository::class);
    }
}
