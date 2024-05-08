<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
public function testEventJson()
{
$client = static::createClient();

// Test GET request to retrieve a single event JSON
$client->request('GET', '/EventJson/1');

$this->assertEquals(200, $client->getResponse()->getStatusCode());
// Add more assertions based on the expected response
}

public function testAllEvents()
{
$client = static::createClient();

// Test GET request to retrieve all events JSON
$client->request('GET', '/allEvents');

$this->assertEquals(200, $client->getResponse()->getStatusCode());
// Add more assertions based on the expected response
}

// Add more test methods for other actions/routes in EventController

public function testAddEventJson()
{
$client = static::createClient();

// Test POST request to add a new event JSON
$client->request('POST', '/addEventJSON/new', [], [], [], json_encode([
'nomEvenement' => 'Test Event',
'dateDebut' => '2024-05-10',
// Add other required fields for the event
]));

$this->assertEquals(200, $client->getResponse()->getStatusCode());
// Add more assertions based on the expected response
}

public function testUpdateEventJson()
{
$client = static::createClient();

// Test POST request to update an existing event JSON
$client->request('POST', '/updateEventJSON/1', [], [], [], json_encode([
'nomEvenement' => 'Updated Event',
// Add other fields to update for the event
]));

$this->assertEquals(200, $client->getResponse()->getStatusCode());
// Add more assertions based on the expected response
}

// Add more test methods for other actions/routes in EventController

public function testDeleteEventJson()
{
$client = static::createClient();

// Test DELETE request to delete an existing event JSON
$client->request('DELETE', '/deleteEventJSON/1');

$this->assertEquals(200, $client->getResponse()->getStatusCode());
// Add more assertions based on the expected response
}
}
