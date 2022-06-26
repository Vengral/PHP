<?php
/**
 * TransactionService tests.
 */

namespace App\Tests\Service;

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
use App\Repository\UserRepository;
use App\Repository\WalletRepository;
use App\Service\TransactionService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * Class TransactionServiceTest.
 */
class TransactionServiceTest extends KernelTestCase
{
    /**
     * Transaction service.
     *
     * @var TransactionService|object|null
     */
    private ?TransactionService $transactionService;

    /**
     * Transaction repository.
     *
     * @var TransactionRepository|object|null
     */
    private ?TransactionRepository $transactionRepository;

    /**
     * Category repository.
     *
     * @var CategoryRepository|object|null
     */
    private ?CategoryRepository $categoryRepository;

    /**
     * Payment repository.
     *
     * @var PaymentRepository|object|null
     */
    private ?PaymentRepository $paymentRepository;

    /**
     * Wallet repository.
     *
     * @var WalletRepository|object|null
     */
    private ?WalletRepository $walletRepository;

    /**
     * Operation repository.
     *
     * @var OperationRepository|object|null
     */
    private ?OperationRepository $operationRepository;

    /**
     * Tag repository.
     *
     * @var TagRepository|object|null
     */
    private ?TagRepository $tagRepository;

    /**
     * User repository.
     *
     * @var UserRepository|object|null
     */
    private ?UserRepository $userRepository;

    /**
     * Test save.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testSave(): void
    {
        // given
        $expectedTransaction = new Transaction();
        $expectedTransaction->setName('Test Transaction');
        $expectedTransaction->setDate(\DateTime::createFromFormat('Y-m-d', '2021-05-09'));
        $expectedTransaction->setUpdatedAt(new \DateTime('now'));
        $expectedTransaction->setCreatedAt(new \DateTime('now'));
        $expectedTransaction->setPayment($this->createPayment());
        $expectedTransaction->setCategory($this->createCategory());
        $expectedTransaction->setOperation($this->createOperation());
        $expectedTransaction->addTag($this->createTag());
        $expectedTransaction->setWallet($this->createWallet());
        $expectedTransaction->setAmount('1000');
        $expectedTransaction->setAuthor($user = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'user999@example.com'));

        // when
        $this->transactionService->save($expectedTransaction);
        $resultTransaction = $this->transactionRepository->findOneById(
            $expectedTransaction->getId()
        );

        // then
        $this->assertEquals($expectedTransaction, $resultTransaction);
    }
    /**
     * Test delete.
     *
     * @covers \App\Service\Transaction::delete
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testDelete(): void
    {
        // given
        $expectedTransaction = new Transaction();
        $expectedTransaction->setName('Test Transaction');
        $expectedTransaction->setDate((\DateTime::createFromFormat('Y-m-d', '2021-05-09')));
        $expectedTransaction->setAmount('1000');
        $expectedTransaction->setUpdatedAt(new \DateTime('now'));
        $expectedTransaction->setCreatedAt(new \DateTime('now'));
        $expectedTransaction->setCategory($this->createCategory());
        $expectedTransaction->setWallet($this->createWallet('user2@example.com'));
        $expectedTransaction->setOperation($this->createOperation());
        $expectedTransaction->setPayment($this->createPayment());
        $expectedTransaction->addTag($this->createTag());

        $expectedId = $expectedTransaction->getId();
        // self::assertNotNull($this->transactionRepository->findOneById($expectedId));
        // when
        $this->transactionService->delete($expectedTransaction);
        $result = $this->transactionRepository->findOneById($expectedId);

        // then
        $this->assertNull($result);
    }

    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @param array $email User email
     *
     * @return User User entity
     */
    private function createUser(array $roles, string $email): User
    {
        $passwordEncoder = self::$container->get('security.password_encoder');
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setUpdatedAt(new \DateTime('now'));
        $user->setCreatedAt(new \DateTime('now'));
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                'p@55w0rd'
            )
        );
        $userRepository = self::$container->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }

    /**
     * Create Category.
     *
     * @return Category
     */
    private function createCategory()
    {
        $category = new Category();
        $category->setName('TCategory');
        $category->setUpdatedAt(new \DateTime('now'));
        $category->setCreatedAt(new \DateTime('now'));
        $categoryRepository = self::$container->get(CategoryRepository::class);
        $categoryRepository->save($category);

        return $category;
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
     * Create Operation.
     *
     * @return Operation
     */
    private function createOperation()
    {
        $operation = new Operation();
        $operation->setName('TOperation');
        $operation->setUpdatedAt(new \DateTime('now'));
        $operation->setCreatedAt(new \DateTime('now'));
        $operationRepository = self::$container->get(OperationRepository::class);
        $operationRepository->save($operation);

        return $operation;
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
        $tag->setUpdatedAt(new \DateTime('now'));
        $tag->setCreatedAt(new \DateTime('now'));
        $tagRepository = self::$container->get(TagRepository::class);
        $tagRepository->save($tag);

        return $tag;
    }

    /**
     * Set up test.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;
        $this->transactionRepository = $container->get(TransactionRepository::class);
        $this->transactionService = $container->get(TransactionService::class);
        $this->categoryRepository = $container->get(CategoryRepository::class);
        $this->paymentRepository = $container->get(PaymentRepository::class);
        $this->walletRepository = $container->get(WalletRepository::class);
        $this->operationRepository = $container->get(OperationRepository::class);
        $this->tagRepository = $container->get(TagRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
    }
    /**
     * Create Wallet.
     * @param array $user User user
     *
     * @return Wallet
     */
    private function createWallet(string $user = 'userr@example.com')
    {
        $wallet = new Wallet();
        $wallet->setName('TWallet');
        $wallet->setBalance('1000');
        $wallet->setUpdatedAt(new \DateTime('now'));
        $wallet->setCreatedAt(new \DateTime('now'));
        $wallet->setUser($this->createUser([UserRole::ROLE_USER->value], $user));
        $walletRepository = self::$container->get(WalletRepository::class);
        $walletRepository->save($wallet);

        return $wallet;
    }
}
