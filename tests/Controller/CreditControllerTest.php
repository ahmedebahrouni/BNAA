<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreditControllerTest extends WebTestCase
{
    public function testAjouterCredit()
    {
        $client = static::createClient();

        // Simulate a request to the '/credit/CreditFront' route
        $crawler = $client->request('GET', '/credit/CreditFront');

        // Assert that the response is successful (HTTP status code 200)
        $this->assertResponseIsSuccessful();

        // Optionally, assert that the rendered template contains certain elements or text
        $this->assertSelectorTextContains('h1', 'Ajouter Crédit');
    }

    public function testAfficher()
    {
        $client = static::createClient();

        // Simulate a request to the '/afichecredit' route
        $crawler = $client->request('GET', '/afichecredit');

        // Assert that the response is successful (HTTP status code 200)
        $this->assertResponseIsSuccessful();

        // Optionally, assert that the rendered template contains certain elements or text
        $this->assertSelectorTextContains('h1', 'Liste des crédits');
    }

    // You can write additional tests for other controller actions such as 'makeDecision'
}
