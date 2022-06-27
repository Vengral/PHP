<?php
/**
 * User fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixtures.
 */
class UserFixtures extends AbstractBaseFixtures
{
    /**
     * Password hashes.
     */
    private UserPasswordHasherInterface $passwordHarsher;

    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordHasherInterface options $passwordHarsher
     */
    public function __construct(UserPasswordHasherInterface $passwordHarsher)
    {
        $this->passwordHarsher = $passwordHarsher;
    }

    /**
     * Load data.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(10, 'users', function ($i) {
            $user = new User();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER]);
            $user->setPassword(
                $this->passwordHarsher->hashPassword(
                    $user,
                    'user1234'
                )
            );
            $user->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $user->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));

            return $user;
        });

        $this->createMany(3, 'admins', function ($i) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER, UserRole::ROLE_ADMIN]);
            $user->setPassword(
                $this->passwordHarsher->hashPassword(
                    $user,
                    'admin1234'
                )
            );
            $user->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $user->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));

            return $user;
        });

        $manager->flush();
    }
}
