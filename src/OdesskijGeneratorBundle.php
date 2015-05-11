<?php
namespace Odesskij\Bundle\GeneratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * OdesskijGeneratorBundle.
 *
 * @author Vladimir Odesskij <odesskij1992@gmail.com>
 */
class OdesskijGeneratorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
