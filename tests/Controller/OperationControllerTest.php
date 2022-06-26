<?php
/**
 * Operation Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\Operation;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\OperationRepository;
use App\Repository\TransactionRepository;
use App\Tests\WebBaseTestCase;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class OperationControllerTest.
 */
class OperationControllerTest extends WebBaseTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/operation';


    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test index route for anonymous user.
     */
    public function testIndexRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = $this->createUser([UserRole::ROLE_ADMIN->value], 'operationindexuser@example.com');
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
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'Operation_user@example.com');
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
        $user = $this->createUser([UserRole::ROLE_USER->value], '_operationuser2@example.com');
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }


    /**
     * Test show single operation.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testShowOperation(): void
    {
        // given
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'operation_user2@exmaple.com');
        $this->httpClient->loginUser($adminUser);

        $expectedOperation = new Operation();
        $expectedOperation->setName('Test operation');
        $expectedOperation->setCreatedAt(new \DateTime('now'));
        $expectedOperation->setUpdatedAt(new \DateTime('now'));
        $operationRepository = static::getContainer()->get(OperationRepository::class);
        $operationRepository->save($expectedOperation);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $expectedOperation->getId());
        $result = $this->httpClient->getResponse();

        // then
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertSelectorTextContains('td', $expectedOperation->getId());
        // ... more assertions...
    }

    //create operation
    public function testCreateOperation(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value],
            'operation_created_user2@example.com');
        $this->httpClient->loginUser($user);
        $operationOperationName = "createdCategor";
        $operationRepository = static::getContainer()->get(OperationRepository::class);

        $this->httpClient->request('GET', self::TEST_ROUTE . '/create');
        // when
        $this->httpClient->submitForm(
            'Zapisz',
            ['operation' => ['name' => $operationOperationName]]
        );

        // then
        $savedOperation = $operationRepository->findOneByName($operationOperationName);
        $this->assertEquals($operationOperationName,
            $savedOperation->getName());


        $result = $this->httpClient->getResponse();
        $this->assertEquals(302, $result->getStatusCode());

    }

    /**
     * @return void
     */
    public function testEditOperationUnauthorizedUser(): void
    {
        // given
        $expectedHttpStatusCode = 302;

        $operation = new Operation();
        $operation->setName('TestOperation');
        $operation->setCreatedAt(new \DateTime('now'));
        $operation->setUpdatedAt(new \DateTime('now'));
        $operationRepository =
            static::getContainer()->get(OperationRepository::class);
        $operationRepository->save($operation);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE . '/' .
            $operation->getId() . '/edit');
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
    public function testEditOperation(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value],
            'operation_edit_user1@example.com');
        $this->httpClient->loginUser($user);

        $operationRepository =
            static::getContainer()->get(OperationRepository::class);
        $testOperation = new Operation();
        $testOperation->setName('TestOperation');
        $testOperation->setCreatedAt(new \DateTime('now'));
        $testOperation->setUpdatedAt(new \DateTime('now'));
        $operationRepository->save($testOperation);
        $testOperationId = $testOperation->getId();
        $expectedNewOperationName = 'TestOperationEdit';

        $this->httpClient->request('GET', self::TEST_ROUTE . '/' .
            $testOperationId . '/edit');

        // when
        $this->httpClient->submitForm(
            'Edytuj',
            ['operation' => ['name' => $expectedNewOperationName]]
        );

        // then
        $savedOperation = $operationRepository->findOneById($testOperationId);
        $this->assertEquals($expectedNewOperationName,
            $savedOperation->getName());
    }


    /**
     * @throws OptimisticLockException
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws ContainerExceptionInterface
     */
    public function testNewRoutAdminUser(): void
    {
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'operationCreate1@example.com');
        $this->httpClient->loginUser($adminUser);
        $this->httpClient->request('GET', self::TEST_ROUTE . '/');
        $this->assertEquals(301, $this->httpClient->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDeleteOperation(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser([UserRole::ROLE_USER->value],
                'operation_deleted_user1@example.com');
        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $this->httpClient->loginUser($user);

        $operationRepository =
            static::getContainer()->get(OperationRepository::class);
        $testOperation = new Operation();
        $testOperation->setName('TestOperationCreated');
        $testOperation->setCreatedAt(new DateTime('now'));
        $testOperation->setUpdatedAt(new DateTime('now'));
        $operationRepository->save($testOperation);
        $testOperationId = $testOperation->getId();

        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $testOperationId . '/delete');

        //when
        $this->httpClient->submitForm(
            'Usuń'
        );

        // then
        $this->assertNull($operationRepository->findOneByName('TestOperationCreated'));
    }

    /**
     * @return void
     */
    public function testCantDeleteOperation(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser([UserRole::ROLE_USER->value],
                'operation_deleted_user2@example.com');
        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $this->httpClient->loginUser($user);

        $operationRepository =
            static::getContainer()->get(OperationRepository::class);
        $testOperation = new Operation();
        $testOperation->setName('TestOperationCreated2');
        $testOperation->setCreatedAt(new DateTime('now'));
        $testOperation->setUpdatedAt(new DateTime('now'));
        $operationRepository->save($testOperation);
        $testOperationId = $testOperation->getId();

        $this->createTransaction($user, $testOperation);

        //when
        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $testOperationId . '/delete');

        // then
        $this->assertEquals(302, $this->httpClient->getResponse()->getStatusCode());
        $this->assertNotNull($operationRepository->findOneByName('TestOperationCreated2'));
    }

}