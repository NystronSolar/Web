<?php

namespace App\Tests\Controller;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private function getClientRepository(): ?ClientRepository
    {
        $clientRepository = static::getContainer()->get(ClientRepository::class);

        return $clientRepository;
    }

    public function testLoginFormInvalidEmail(): void
    {
        $this->sendLogin([
            'email' => 'wrong@user.com',
            'password' => 'wrong',
        ]);

        $this->assertLoginFails();
    }

    public function testLoginFormInvalidPassword(): void
    {
        $this->sendLogin([
            'email' => 'admin@user.com',
            'password' => 'wrong',
        ]);

        $this->assertLoginFails();
    }

    public function testLoginForm(): void
    {
        $this->sendLogin([
            'email' => 'admin@user.com',
            'password' => 'admin',
        ]);

        $this->assertLoginSuccess();
    }

    public function testLogout(): void
    {
        $client = static::createClient();

        $testUser = $this->getClientRepository()->findOneByEmail('client@user.com');

        $client->loginUser($testUser);
        $client->request('GET', '/security/logout');

        $this->assertResponseRedirects('/security/login', 302);
        $this->assertNull($client->getRequest()->getUser());
    }

    private function sendLogin(array $values): void
    {
        $client = static::createClient();
        $client->request('GET', '/security/login');

        $client->submitForm('Sign in', $values);
    }

    public static function assertLoginFails(): void
    {
        self::assertResponseRedirects('/security/login', 302);
    }

    public static function assertLoginSuccess(): void
    {
        self::assertResponseStatusCodeSame(302);
        self::assertResponseRedirects('/dashboard/');
    }
}