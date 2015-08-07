<?php
namespace Dogs\DogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DogBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
