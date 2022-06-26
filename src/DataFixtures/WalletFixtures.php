<?php
/**
 * Wallet fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Wallet;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class WalletFixtures.
 */
class WalletFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(20, 'wallets', function ($i) {
            $wallet = new Wallet();
            $wallet->setName($this->faker->word);
            $wallet->setBalance($this->faker->numberBetween(1, 700000));
            $wallet->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $wallet->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $wallet->setUser($this->getRandomReference('users'));

            return $wallet;
        });

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array Array of dependencies
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
