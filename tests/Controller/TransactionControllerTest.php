<?php
/**
 * Category Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Entity\Operation;
use App\Entity\Payment;
use App\Entity\Tag;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\CategoryRepository;
use App\Repository\OperationRepository;
use App\Repository\PaymentRepository;
use App\Repository\TagRepository;
use App\Repository\TransactionRepository;
use App\Repository\WalletRepository;
use App\Tests\WebBaseTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class CategoryControllerTest.
 */
class TransactionControllerTest extends WebBaseTestCase
{
    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

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
        $expectedStatusCode = 302;

        // when
        $this->httpClient->request('GET', '/transaction/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for anonymous user.
     */
    public function testIndexRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $admin = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'transactioAdmin@example.com');
        $this->httpClient->loginUser($admin);
        // when
        $this->httpClient->request('GET', '/transaction/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }
    /**
     * Test Show Transaction
     */
    public function testShowTransaction(): void
    {
        // given
        $expectedStatusCode = 302;
        $expectedTransaction = $this->createTransaction();
        $transactionRepository = self::$container->get(TransactionRepository::class);
        $id = $expectedTransaction->getId();
        // when
        $this->httpClient->request('GET', '/transaction/'.$id);
        $result = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $result);
        $transactionRepository->delete($expectedTransaction);
    }

    /**
     * Test index route for anonymous user.
     */
    public function testIndexRouteSearch(): void
    {
        // given
        $expectedStatusCode = 302;

        // when
        $aa = $this->httpClient->request('GET', '/transaction/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test create transaction for admin user.
     */
    public function testCreateTransactionAdminUser(): void
    {
        // given
        $expectedStatusCode = 301;
        $admin = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'transaction_user1@example.com');
        $this->logIn($admin);
        // when
        $this->httpClient->request('GET', '/transaction/create/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test create transaction for admin user.
     */
    public function testCreateTransactionNonAdmin(): void
    {
        // given
        $expectedStatusCode = 301;
        $admin = $this->createUser([UserRole::ROLE_USER->value], 'user01@example.com');
        $this->logIn($admin);
        // when
        $this->httpClient->request('GET', '/transaction/create/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Edit transaction.
     */
    public function testEditTransaction(): void
    {
        $transaction = $this->createTransaction();

        $transaction->setName('ChangedTransactionName');
        $transaction->setUpdatedAt(new \DateTime('now'));
        $transaction->setUpdatedAt(new \DateTime('now'));

        $transactionRepository = self::$container->get(TransactionRepository::class);
        $transactionRepository->save($transaction);

        $expected = 'ChangedTransactionName';

        $this->assertEquals($expected, $transactionRepository->findByName($expected)->getName());
        $transactionRepository->delete($transaction);
    }

    /**
     * Delete transaction.
     */
    public function testDeleteTransaction(): void
    {
        $transaction = $this->createTransaction();
        $transaction->setName('ChangedTransactionName');

        $transactionRepository = self::$container->get(TransactionRepository::class);
        $transactionRepository->save($transaction);

        $expected = new Transaction();

        $transactionRepository->delete($transaction);

        $this->assertEquals($expected, $transactionRepository->findByName('ChangedTransactionName')->getName());
        $transactionRepository->delete($transaction);
    }

    /**
     * Create Wallet.
     * @return Wallet
     */
    private function createWallet(): Wallet
    {
        $wallet = new Wallet();
        $wallet->setName('TWallet');
        $wallet->setBalance('1000');
        $wallet->setUser($this->createUser([UserRole::ROLE_USER->value], 'uu@example.com'));
        $walletRepository = self::$container->get(WalletRepository::class);
        $walletRepository->save($wallet);

        return $wallet;
    }

    /**
     * Create transaction.
     * @return Transaction
     */
    private function createTransaction(): Transaction
    {
        $transaction = new Transaction();
        $transaction->setName('TName');
        $transaction->setDate(\DateTime::createFromFormat('Y-m-d', '2021-05-09'));
        $transaction->setAmount('11');
        $transaction->setCategory($this->createCategory());
        $transaction->setWallet($this->createWallet());
        $transaction->setOperation($this->createOperation());
        $transaction->setPayment($this->createPayment());
        $transaction->addTag($this->createTag());
        $transaction->setAuthor($this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'transaction3@example.com'));

        $transactionRepository = self::$container->get(TransactionRepository::class);
        $transactionRepository->save($transaction);

        return $transaction;
    }

    /**
     * Simulate user log in.
     *
     * @param User $user User entity
     */
    private function logIn(User $user): void
    {
        $session = self::$container->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->httpClient->getCookieJar()->set($cookie);
    }

    /**
     * Create Tag.
     *
     * @return Tag
     */
    private function createTag()
    {
        $tag = new Tag();
        $tag->setName('TTag');
        $tagRepository = self::$container->get(TagRepository::class);
        $tagRepository->save($tag);

        return $tag;
    }
    /**
     * Create Operation.
     *
     * @return Operation
     */
    private function createOperation()
    {
        $operation = new Operation();
        $operation->setName('TOperation');
        $operationRepository = self::$container->get(OperationRepository::class);
        $operationRepository->save($operation);

        return $operation;
    }
    /**
     * Create Payment.
     *
     * @return Payment
     */
    private function createPayment()
    {
        $payment = new Payment();
        $payment->setName('TPayment');
        $paymentRepository = self::$container->get(PaymentRepository::class);
        $paymentRepository->save($payment);

        return $payment;
    }
    /**
     * Create category.
     * @return Category
     */
    private function createCategory()
    {
        $category = new Category();
        $category->setName('TName');
        $category->setUpdatedAt(new \DateTime('now'));
        $category->setCreatedAt(new \DateTime('now'));
        $categoryRepository = self::$container->get(CategoryRepository::class);
        $categoryRepository->save($category);

        return $category;
    }
}
