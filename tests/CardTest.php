<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CardTest extends WebTestCase
{
    public function testGetCollection(): void
    {
        $client = static::createClient();
        $client->request('GET',  '/api/cards.json');

        $this->assertResponseIsSuccessful();

        //$this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

    }
}