<?php

namespace Acme\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ToolControllerTest extends WebTestCase
{
    public function testGetenabled()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/getEnabled');
    }

}
