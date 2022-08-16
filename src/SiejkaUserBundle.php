<?php

namespace Siejka\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Siejka\UserBundle\DependencyInjection\SiejkaUserExtension;

class SiejkaUserBundle extends Bundle
{ 
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
    
    public function getContainerExtension()
    {
        return new SiejkaUserExtension();
    }
}