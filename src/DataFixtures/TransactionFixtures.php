<?php
/**
 * Transaction fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Transaction;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TransactionFixtures.
 */
class TransactionFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(50, 'transaction', function () {
            $transaction = new Transaction();
            $transaction->setName($this->faker->sentence(3, 64));
            $transaction->setDate($this->faker->dateTimeThisYear);
            $transaction->setAmount($this->faker->numberBetween(1, 900000));
            $transaction->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $transaction->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $transaction->setCategory($this->getRandomReference('categories'));
            $transaction->setOperation($this->getRandomReference('operations'));
            $transaction->setPayment($this->getRandomReference('payments'));
            $transaction->setWallet($this->getRandomReference('wallets'));
            $tags = $this->getRandomReferences(
                'tags',
                $this->faker->numberBetween(0, 5)
            );

            foreach ($tags as $tag) {
                $transaction->addTag($tag);
            }

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $transaction->setAuthor($author);

            return $transaction;
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
        return [CategoryFixtures::class, OperationFixtures::class, PaymentFixtures::class, WalletFixtures::class, TagFixtures::class, UserFixtures::class];
    }
}
