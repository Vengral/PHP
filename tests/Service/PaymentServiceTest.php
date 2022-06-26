<?php
/**
 * PaymentService tests.
 */

namespace App\Tests\Service;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use App\Repository\TransactionRepository;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class PaymentServiceTest.
 */
class PaymentServiceTest extends KernelTestCase
{
    /**
     * Payment service.
     *
     * @var PaymentService|object|null
     */
    private ?PaymentService $paymentService;

    /**
     * Payment repository.
     *
     * @var PaymentRepository|object|null
     */
    private ?PaymentRepository $paymentRepository;

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
        $expectedPayment = new Payment();
        $expectedPayment->setName('Test Payment');

        // when
        $this->paymentService->save($expectedPayment);
        $resultPayment = $this->paymentRepository->findOneById(
            $expectedPayment->getId()
        );

        // then
        $this->assertEquals($expectedPayment, $resultPayment);
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
        $expectedPayment = new Payment();
        $expectedPayment->setName('Test Payment');
        $expectedPayment->setCreatedAt(new \DateTime('now'));
        $expectedPayment->setUpdatedAt(new \DateTime('now'));
        $this->paymentRepository->save($expectedPayment);
        $expectedId = $expectedPayment->getId();

        // when
        $this->paymentService->delete($expectedPayment);
        $result = $this->paymentRepository->findOneById($expectedId);

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
        $expectedResultSize = 0;
        // when
        $result = $this->paymentService->getPaginatedList($page);

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
        $this->paymentRepository = $container->get(PaymentRepository::class);
        $this->paymentService = $container->get(PaymentService::class);
        $this->transactionRepository = $container->get(TransactionRepository::class);
    }
}
