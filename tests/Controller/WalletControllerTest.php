<?php
/**
 * Wallet Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\Wallet;
use App\Repository\WalletRepository;
use App\Tests\WebBaseTestCase;
use DateTime;

/**
 * Class WalletControllerTest.
 */
class WalletControllerTest extends WebBaseTestCase
{


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
        $user = $this->createUser([UserRole::ROLE_ADMIN->value], 'walletindexuser@example.com');
        $this->logIn($user);
        // when
        $this->httpClient->request('GET', '/wallet');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for admin user.
     */
    public function testIndexRouteAdminUser(): void
    {
        $expectedStatusCode = 301;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'user432@example.com');
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->request('GET', '/wallet/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test create film for admin user.
     */
    public function testCreateWalletAdminUser(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value],
            'wallet_created_user1@example.com');
        $this->httpClient->loginUser($user);
        $expected = "name";
        $walletRepository = self::getContainer()->get(WalletRepository::class);
        // when
        $this->httpClient->request('GET', '/wallet/create');

        $this->httpClient->submitForm(
            'utworzenie',
            ['wallet' =>
                [
                    'name' => $expected,
                    'balance' => 300,
                ]
            ]
        );

        $this->assertEquals($expected, $walletRepository->findOneByName($expected)->getName());

    }


    /**
     * Test index route for non authorized user FOR NEW Wallet.
     */
    public function testIndexRouteNonAuthorizedUser(): void
    {
        // given
        $expectedStatusCode = 301;
        $user = $this->createUser(['ROLE_USER'], 'user124@example.com');
        $this->logIn($user);

        // when
        $this->httpClient->request('GET', '/wallet/create/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * @return void
     */
    public function testEditWallet(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value],
            'wallet_created_user2@example.com');
        $this->httpClient->loginUser($user);
        $wallet = new Wallet();
        $wallet->setName('TestWallet123');
        $wallet->setUser($user );
        $wallet->setBalance(2000);
        $walletRepository = self::getContainer()->get(WalletRepository::class);
        $walletRepository->save($wallet);
        $expected = 'TestChangedWallet123.';
        // when

        $this->httpClient->request('GET', '/wallet/' .
            $wallet->getId() . '/edit');

        $this->httpClient->submitForm(
            'Edytuj',
            ['wallet' =>
                [
                    'name' => $expected,
                    'balance' => 300,
                ]
            ]
        );

        $this->assertEquals($expected, $walletRepository->findOneByName($expected)->getName());

    }



}