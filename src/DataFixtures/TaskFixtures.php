<?php

// src/DataFixtures/TaskFixtures.php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créez une tâche pour un utilisateur fictif avec des données aléatoires
        $user = $this->getReference('userAdmin'); // Remplacez 'admin' par le nom d'utilisateur de l'utilisateur approprié

        $task = new Task();
        $task->setTitle($faker->sentence);
        $task->setContent($faker->paragraph);
        $task->setUser($user);

        $manager->persist($task);

        // Créez d'autres tâches de test
        for ($i = 0; $i < 10; $i++) {
            $user = $this->getReference('user'.$i);
            
            $task = new Task();
            $task->setTitle($faker->sentence);
            $task->setContent($faker->paragraph);
            $task->setUser($user);
            
            $manager->persist($task);
        }

        $manager->flush();
    }
}
