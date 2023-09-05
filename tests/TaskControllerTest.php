<?php

namespace App\Tests;

use App\Entity\Task;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testTaskListPage(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('mela.dussenne@gmail.com');
        $client->loginUser($testUser);

        $client->request('GET', '/tasks');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();

        // Vérifiez la présence du bouton "Créer une tâche"
        $this->assertSelectorExists('.btn-info.pull-right:contains("Créer une tâche")');
    }


    public function testTaskCreation(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('mela.dussenne@gmail.com');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/create');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();
        // Fill in the form fields here
        $form['task[title]'] = 'Nouvelle tâche';
        $form['task[content]'] = 'Description de la tâche';

        $client->submit($form);

        $this->assertResponseRedirects('/tasks');
        // You can add assertions for flash messages or other post-submit behavior
        // $this->assertNotEmpty($client->getContainer()->get('session.factory')->getFlashBag()->get('success'));
    }

    public function testDeleteTaskByAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $adminUser = $userRepository->findOneByEmail('mela.dussenne@gmail.com');
        $client->loginUser($adminUser);

        // Créez une tâche avec un utilisateur anonyme
        $taskTitle = 'Tâche de l\'utilisateur anonyme';
        $taskContent = 'Contenu de la tâche de l\'utilisateur anonyme';

        $crawler = $client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = $taskTitle;
        $form['task[content]'] = $taskContent;

        $client->submit($form);

        // Récupérez la tâche créée
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $taskRepository = $entityManager->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => $taskTitle]);

        // Assurez-vous que la tâche a bien été créée
        $this->assertInstanceOf(Task::class, $task);

        // Maintenant, testez la suppression de la tâche par l'administrateur
        $client->request('GET', '/tasks/' . $task->getId() . '/delete');

        $this->assertResponseRedirects('/tasks');
        // Vous pouvez ajouter des assertions supplémentaires pour vérifier que la tâche a été supprimée avec succès.
    }

    public function testDeleteTask(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('mela.dussenne@gmail.com');
        $client->loginUser($testUser);

        // Créez une tâche
        $taskTitle = 'Tâche de test à supprimer';
        $taskContent = 'Contenu de la tâche de test à supprimer';

        $crawler = $client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = $taskTitle;
        $form['task[content]'] = $taskContent;

        $client->submit($form);

        // Récupérez la tâche créée
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $taskRepository = $entityManager->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => $taskTitle]);

        // Assurez-vous que la tâche a bien été créée
        $this->assertInstanceOf(Task::class, $task);

        // Maintenant, testez la suppression de la tâche
        $client->request('GET', '/tasks/' . $task->getId() . '/delete');

        $this->assertResponseRedirects('/tasks');

        // Vous pouvez ajouter des assertions supplémentaires pour vérifier que la tâche a été supprimée avec succès.

        // Vérifiez que la tâche n'existe plus dans la base de données
        $deletedTask = $taskRepository->find($task->getId());
        $this->assertNull($deletedTask);
    }

    public function testAddTask(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('mela.dussenne@gmail.com');
        $client->loginUser($testUser);

        // Récupérez le nombre de tâches avant d'ajouter une nouvelle tâche
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $taskRepository = $entityManager->getRepository(Task::class);
        $initialTaskCount = count($taskRepository->findAll());

        // Accédez à la page de création de tâches
        $crawler = $client->request('GET', '/tasks/create');

        // Remplissez le formulaire pour ajouter une nouvelle tâche
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Nouvelle tâche';
        $form['task[content]'] = 'Description de la nouvelle tâche';

        // Soumettez le formulaire
        $client->submit($form);

        // Récupérez le nombre de tâches après avoir ajouté une nouvelle tâche
        $updatedTaskCount = count($taskRepository->findAll());

        // Assurez-vous que le nombre de tâches a augmenté après la soumission
        $this->assertEquals($initialTaskCount + 1, $updatedTaskCount);

    }

    public function testTaskCountOnPage(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('mela.dussenne@gmail.com');
        $client->loginUser($testUser);

        // Récupérez le nombre de tâches dans la base de données
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $taskRepository = $entityManager->getRepository(Task::class);
        $taskCountInDatabase = count($taskRepository->findAll());

        // Accédez à la page de la liste des tâches
        $crawler = $client->request('GET', '/tasks');

        // Récupérez le nombre de tâches affiché sur la page
        $taskCountOnPage = $crawler->filter('.task')->count();

        // Assurez-vous que le nombre de tâches sur la page correspond au nombre de tâches dans la base de données
        $this->assertEquals($taskCountInDatabase, $taskCountOnPage);
        
    }




}