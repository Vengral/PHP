<?php
/**
 * Payment Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\Payment;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\PaymentRepository;
use App\Repository\TransactionRepository;
use App\Tests\WebBaseTestCase;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class PaymentControllerTest.
 */
class PaymentControllerTest extends WebBaseTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/payment';


    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * @return void
     */
    public function testIndexRouteAnonymousUser(): void
    {
        // given
        $user = null;
        $expectedStatusCode = 200;
        try {
            $user = $this->createUser([UserRole::ROLE_ADMIN->value], 'paymentindexuser@example.com');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException $e) {
        }
        $this->logIn($user);
        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testIndexRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'payment_user@example.com');
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for non-authorized user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testIndexRouteNonAuthorizedUser(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value], 'payment_user2@example.com');
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }



    /**
     * Test show single payment.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testShowPayment(): void
    {
        // given
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'payment_user2@exmaple.com');
        $this->httpClient->loginUser($adminUser);

        $expectedPayment = new Payment();
        $expectedPayment->setName('Test payment');
        $expectedPayment->setCreatedAt(new DateTime('now'));
        $expectedPayment->setUpdatedAt(new DateTime('now'));
        $paymentRepository = static::getContainer()->get(PaymentRepository::class);
        $paymentRepository->save($expectedPayment);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $expectedPayment->getId());
        $result = $this->httpClient->getResponse();

        // then
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertSelectorTextContains('td', $expectedPayment->getId());
        // ... more assertions...
    }

    //create payment
    public function testCreatePayment(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value],
            'payment_created_user2@example.com');
        $this->httpClient->loginUser($user);
        $paymentPaymentName = "createdCategor";
        $paymentRepository = static::getContainer()->get(PaymentRepository::class);

        $this->httpClient->request('GET', self::TEST_ROUTE . '/create');
        // when
        $this->httpClient->submitForm(
            'Zapisz',
            ['payment' => ['name' => $paymentPaymentName]]
        );

        // then
        $savedPayment = $paymentRepository->findOneByName($paymentPaymentName);
        $this->assertEquals($paymentPaymentName,
            $savedPayment->getName());


        $result = $this->httpClient->getResponse();
        $this->assertEquals(302, $result->getStatusCode());

    }

    /**
     * @return void
     */
    public function testEditPaymentUnauthorizedUser(): void
    {
        // given
        $expectedHttpStatusCode = 302;

        $payment = new Payment();
        $payment->setName('TestPayment');
        $payment->setCreatedAt(new DateTime('now'));
        $payment->setUpdatedAt(new DateTime('now'));
        $paymentRepository =
            static::getContainer()->get(PaymentRepository::class);
        $paymentRepository->save($payment);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE . '/' .
            $payment->getId() . '/edit');
        $actual = $this->httpClient->getResponse();

        // then

        $this->assertEquals($expectedHttpStatusCode,
            $actual->getStatusCode());

    }


    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testEditPayment(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value],
            'payment_edit_user1@example.com');
        $this->httpClient->loginUser($user);

        $paymentRepository =
            static::getContainer()->get(PaymentRepository::class);
        $testPayment = new Payment();
        $testPayment->setName('TestPayment');
        $testPayment->setCreatedAt(new DateTime('now'));
        $testPayment->setUpdatedAt(new DateTime('now'));
        $paymentRepository->save($testPayment);
        $testPaymentId = $testPayment->getId();
        $expectedNewPaymentName = 'TestPaymentEdit';

        $this->httpClient->request('GET', self::TEST_ROUTE . '/' .
            $testPaymentId . '/edit');

        // when
        $this->httpClient->submitForm(
            'Edytuj',
            ['payment' => ['name' => $expectedNewPaymentName]]
        );

        // then
        $savedPayment = $paymentRepository->findOneById($testPaymentId);
        $this->assertEquals($expectedNewPaymentName,
            $savedPayment->getName());
    }


    /**
     * @throws OptimisticLockException
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws ContainerExceptionInterface
     */
    public function testNewRoutAdminUser(): void
    {
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'paymentCreate1@example.com');
        $this->httpClient->loginUser($adminUser);
        $this->httpClient->request('GET', self::TEST_ROUTE . '/');
        $this->assertEquals(301, $this->httpClient->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDeletePayment(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value],
            'payment_deleted_user1@example.com');
        $this->httpClient->loginUser($user);

        $paymentRepository =
            static::getContainer()->get(PaymentRepository::class);
        $testPayment = new Payment();
        $testPayment->setName('TestPaymentCreated');
        $testPayment->setCreatedAt(new DateTime('now'));
        $testPayment->setUpdatedAt(new DateTime('now'));
        $paymentRepository->save($testPayment);
        $testPaymentId = $testPayment->getId();

        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $testPaymentId . '/delete');

        //when
        $this->httpClient->submitForm(
            'Usuń'
        );

        // then
        $this->assertNull($paymentRepository->findOneByName('TestPaymentCreated'));
    }

    /**
     * @return void
     */
    public function testCantDeletePayment(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser([UserRole::ROLE_USER->value],
                'payment_deleted_user2@example.com');
        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $this->httpClient->loginUser($user);

        $paymentRepository =
            static::getContainer()->get(PaymentRepository::class);
        $testPayment = new Payment();
        $testPayment->setName('TestPaymentCreated2');
        $testPayment->setCreatedAt(new DateTime('now'));
        $testPayment->setUpdatedAt(new DateTime('now'));
        $paymentRepository->save($testPayment);
        $testPaymentId = $testPayment->getId();

        $this->createTransaction($user, $testPayment);

        //when
        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $testPaymentId . '/delete');

        // then
        $this->assertEquals(302, $this->httpClient->getResponse()->getStatusCode());
        $this->assertNotNull($paymentRepository->findOneByName('TestPaymentCreated2'));
    }

    /**
     * @param User $user
     * @param $payment
     * @return void
     */
    private function createTransaction(User $user, $payment)
    {
        $transaction = new Transaction();
        $transaction->setName('TName');
        $transaction->setDate(DateTime::createFromFormat('Y-m-d', "2021-05-09"));
        $transaction->setAmount('11');
        $transaction->setPayment($payment);
        $transaction->setWallet($this->createWallet($user));
        $transaction->setOperation($this->createOperation());
        $transaction->setCategory($this->createCategory());
        $transaction->addTag($this->createTag());
        $transaction->setAuthor($user);

        $transactionRepository = self::getContainer()->get(TransactionRepository::class);
        $transactionRepository->save($transaction);

    }
}