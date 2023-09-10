<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créez un utilisateur administrateur avec des données fixes
        $admin = new User();
        $admin->setUsername('fleur');
        $admin->setEmail('mela.dussenne@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(password_hash('password', PASSWORD_BCRYPT)); // Encodez le mot de passe manuellement
        $manager->persist($admin);
        $this->addReference('userAdmin', $admin);

    //   // Créez un utilisateur avec des données fixes
    //   $userfleur = new User();
    //   $userfleur->setUsername('userfleur');
    //   $userfleur->setEmail('meloch.dussenne@gmail.com');
    //   $userfleur->setRoles(['ROLE_USER']);
    //   $userfleur->setPassword(password_hash('password', PASSWORD_BCRYPT)); // Encodez le mot de passe manuellement
    //   $manager->persist($userfleur);
    //   $this->addReference('userFleur', $userfleur);

        // Créez d'autres utilisateurs de test avec des données aléatoires
        for ($i = 0; $i < 10; $i++) {
            $user = new User();

            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT)); // Encodez le mot de passe manuellement
            $manager->persist($user);
            $this->addReference('user'.$i, $user);
        }

        // Persistez les utilisateurs
        $manager->flush();
    }
}
