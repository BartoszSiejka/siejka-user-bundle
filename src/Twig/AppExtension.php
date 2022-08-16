<?php

/*
 * This file is part of the ||application-name|| app.
 *
 * (c) Bartosz Siejka
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siejka\UserBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_env', [$this, 'getEnvironmentVariable']),
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