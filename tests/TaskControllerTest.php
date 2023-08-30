<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testTaskListPage(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'fleur',
            'PHP_AUTH_PW' => 'test',
        ]);

        $client->request('GET', '/tasks');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();

        // Vérifiez la présence du bouton "Créer une tâche"
        $this->assertSelectorExists('.btn-info.pull-right:contains("Créer une tâche")');
    }


    public function testTaskCreation(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'fleur',
            'PHP_AUTH_PW' => 'test',
        ]);

        $crawler = $client->request('GET', '/tasks/create');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();
        // Fill in the form fields here
        $form['task[title]'] = 'Nouvelle tâche';
        $form['task[description]'] = 'Description de la tâche';

        $client->submit($form);

        $this->assertResponseRedirects('/tasks');
        // You can add assertions for flash messages or other post-submit behavior
        $this->assertNotEmpty($client->getContainer()->get('session')->getFlashBag()->get('success'));
    }

    // Add more test methods for other actions (edit, toggle, delete) in a similar manner
}