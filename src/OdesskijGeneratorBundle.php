<?php
namespace Odesskij\Bundle\GeneratorBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
