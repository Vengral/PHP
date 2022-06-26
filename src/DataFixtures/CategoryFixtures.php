<?php
/**
 * Category fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

/**
 * Class CategoryFixtures.
 */
class CategoryFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(20, 'categories', function ($i) {
            $category = new Category();
            $category->setName($this->faker->word);
            $category->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $category->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));

            return $category;
        });

        $manager->flush();
    }
}
