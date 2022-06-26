<?php
/**
 * Payment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Payment;
use Doctrine\Persistence\ObjectManager;

/**
 * Class PaymentFixtures.
 */
class PaymentFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(20, 'payments', function ($i) {
            $payment = new Payment();
            $payment->setName($this->faker->word);
            $payment->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $payment->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));

            return $payment;
        });

        $manager->flush();
    }
}
