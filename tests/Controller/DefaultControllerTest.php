<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Індекс'); //TODO: check other translations
    }

    public function testAbout(): void
    {
        $client = static::createClient();
        $client->request('GET', '/404');
        $this->assertResponseStatusCodeSame(404);
    }
}