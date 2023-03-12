<?php

namespace App\Helper;

use App\Validator\CPFValidator;

class Formatter
{
    public static function addStyleCPF(string $currentCPF): string|false
    {
        $cpf = substr_replace($currentCPF, '.', 3, 0);
        $cpf = substr_replace($cpf, '.', 7, 0);
        $cpf = substr_replace($cpf, '-', 11, 0);

        return $cpf;
    }

    public static function removeStyleCPF(string $currentCPF): string|false
    {
        if (!CPFValidator::isValid($currentCPF)) {
            return false;
        }

        $cpf = str_replace('.', '', $currentCPF);
        $cpf = str_replace('-', '', $cpf);

        return $cpf;
    }
}
