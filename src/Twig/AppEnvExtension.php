<?php

// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppEnvExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('env', [$this, 'getEnvironmentVariable']),
        ];
    }

    /**
     * Return the value of the requested environment variable.
     *
     * @param String $varname
     * @return String
     */
    public function getEnvironmentVariable($varname)
    {
        return $_ENV[$varname];
    }
}