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

}