<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GestionuserControllerTest extends WebTestCase
{
public function testIndex()
{
$client = static::createClient();

$client->request('GET', '/gestionuser');

$this->assertEquals(200, $client->getResponse()->getStatusCode());
// Add more assertions based on the expected response
}

// Add more test methods for other actions/routes in GestionuserController

public function testLogin()
{
$client = static::createClient();

$client->request('POST', '/loginnn', [], [], [], json_encode([
'email' => 'test@example.com',
'mdp' => 'testpassword',
// Add other required fields for login
]));

$this->assertEquals(200, $client->getResponse()->getStatusCode());
// Add more assertions based on the expected response
}

public function testAjouterUser()
{
$client = static::createClient();

$client->request('POST', '/ajouteruser', [], [], [], json_encode([
'nom' => 'Test',
'prenom' => 'User',
'mdp' => 'testpassword',
'email' => 'test@example.com',
// Add other required fields for user creation
]));

$this->assertEquals(200, $client->getResponse()->getStatusCode());
// Add more assertions based on the expected response
}

// Add more test methods for other actions/routes in GestionuserController
}
