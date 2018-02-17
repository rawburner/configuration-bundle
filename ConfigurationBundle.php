<?php

namespace ConfigurationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use ConfigurationBundle\DependencyInjection\ConfigurationExtension;

class ConfigurationBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ConfigurationExtension();
    }
}
