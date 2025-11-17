<?php

// Used for creating simulated stations and weather data
// This file is used to populate the database with initial data for testing purposes.

namespace App\DataFixtures;

use DateTime;
use DatePeriod;
use DateInterval;
use DateTimeZone;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
        public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function load(ObjectManager $manager):void
    {
        echo "Creating test user: admin / password\n";
        $user = new Users();
        $user->setEmail('test@example.com');
        $user->setName('admin');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();

        echo "Creating API test user: api_user / password\n";
        $user = new Users();
        $user->setEmail('apiuser@example.com');
        $user->setName('api_user');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_API_USER']);
        $manager->persist($user);

        $manager->flush();
    }
}
