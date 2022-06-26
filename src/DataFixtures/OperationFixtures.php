<?php
/**
 * Operation fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Operation;
use Doctrine\Persistence\ObjectManager;

/**
 * Class OperationFixtures.
 */
class OperationFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(20, 'operations', function ($i) {
            $operation = new Operation();
            $operation->setName($this->faker->word);
            $operation->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $operation->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));

            return $operation;
        });

        $manager->flush();
    }
}
