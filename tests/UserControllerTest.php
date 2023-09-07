<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function clientAdminLogin($email = 'mela.dussenne@gmail.com')
    {
        $client = static::createClient();
        //active le profile
        $client->enableProfiler();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail($email);
        $client->loginUser($testUser);

        return $client;
    }

    private function getAdminUser(): User
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneBy(['username' => 'fleur']); // Recherchez l'utilisateur par son nom d'utilisateur
    }

    public function testListAction()
    {
        $client = $this->clientAdminLogin();
        $client->request('GET', '/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateAction()
    {
        $client = $this->clientAdminLogin();

        $crawler = $client->request('GET', '/users/create');

        // Créez un formulaire de test pour la création d'un utilisateur et soumettez-le.
        $form = $crawler->selectButton('Ajouter')->form();
        // Remplissez le formulaire avec les données nécessaires pour la création d'un utilisateur.
        $form['user[username]'] = 'sddddddxxxddds';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'melddxdddxxxxddae@gmail.com';
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect('/users'));
    }

    public function testEditAction()
    {
        // Assurez-vous d'avoir un utilisateur existant pour le tester
        $admin = $this->getAdminUser();

        if ($admin) {
            $userId = $admin->getId(); // Obtenez l'ID de l'utilisateur administrateur

            // Utilisez l'ID de l'utilisateur administrateur dans votre requête
            $client = $this->clientAdminLogin();
            $crawler = $client->request('GET', '/users/' . $userId . '/edit');

            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            // Créez un formulaire de test pour la modification d'un utilisateur et soumettez-le.
            $form = $crawler->selectButton('Modifier')->form();
            // Modifiez les champs du formulaire selon vos besoins.
            $form['user[username]'] = 'utilisateur_modifie';
            $form['user[password][first]'] = 'nouveau_mot_de_passe';
            $form['user[password][second]'] = 'nouveau_mot_de_passe';
            $client->submit($form);

            $this->assertTrue($client->getResponse()->isRedirect('/users'));

            // Chargez à nouveau l'utilisateur depuis la base de données après la modification.
            $updatedUser = $this->getContainer()->get('doctrine')->getRepository(User::class)->find($userId);

            // Vérifiez que le rôle de l'utilisateur a été modifié correctement.
            $this->assertContains('ROLE_USER', $updatedUser->getRoles());
        } else {
            $this->fail("L'utilisateur administrateur n'a pas été trouvé.");
        }
    }

    public function testAccessFailed()
    {
        // Créez un client en tant qu'utilisateur avec le rôle "ROLE_USER".
        $client = $this->clientAdminLogin("mela.dussenne@gmail.com");

        // Essayez d'accéder à la page de gestion des utilisateurs (par exemple, la liste des utilisateurs).
        $client->request('GET', '/users');

        // Vérifiez que l'accès est refusé (doit renvoyer une redirection 302 ou autre statut d'erreur).
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
