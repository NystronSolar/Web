<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    public function testRoutesWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/en/dashboard');

        $this->assertResponseRedirects('/en/security/login', 302);
    }
}
