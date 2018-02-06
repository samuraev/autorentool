<?php

namespace Autorentool\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AutorentoolUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
