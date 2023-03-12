<?php

namespace App\Tests\Helper;

use App\Helper\Formatter;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create('pt_BR');
    }

    public function testAddStyleCPF()
    {
        $fakerStyledCPF = $this->faker->cpf();
        $fakerNoStyleCPF = Formatter::removeStyleCPF($fakerStyledCPF);

        $result = Formatter::addStyleCPF($fakerNoStyleCPF);

        $this->assertNotFalse($result);
        $this->assertSame($fakerStyledCPF, $result);
    }

    public function testRemoveStyleCPF()
    {
        $fakerNoStyleCPF = $this->faker->cpf(false);
        $fakerStyledCPF = Formatter::addStyleCPF($fakerNoStyleCPF);

        $result = Formatter::removeStyleCPF($fakerStyledCPF);

        $this->assertNotFalse($result);
        $this->assertSame($fakerNoStyleCPF, $result);
    }
}
