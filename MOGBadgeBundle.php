<?php

namespace MOG\Bundle\BadgeBundle;

use MOG\Bundle\BadgeBundle\DependencyInjection\Compiler\RegisterBadgeDefinitionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MOGBadgeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterBadgeDefinitionsPass());
    }
}
