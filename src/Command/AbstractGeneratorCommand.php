<?php
namespace Odesskij\Bundle\GeneratorBundle\Command;

use Odesskij\Bundle\GeneratorBundle\Service\Validator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * AbstractGeneratorCommand.
 *
 * @author Vladimir Odesskij <odesskij1992@gmail.com>
 */
abstract class AbstractGeneratorCommand extends ContainerAwareCommand
{
    CONST SKELETON_TWIG_NAMESPACE = 'SKELETON';
    CONST SUGGEST_TWIG_NAMESPACE = 'SUGGEST';

    /**
     * Write file
     *
     * @param $template
     * @param $target
     * @param $parameters
     * @return int
     */
    protected function renderFile($template, $target, $parameters)
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        return file_put_contents($target, $this->render($template, $parameters));
    }

    /**
     * Get file content
     *
     * @param $template
     * @param $parameters
     * @return string
     */
    protected function render($template, $parameters)
    {
        $twig = $this->getTwigEnvironment();
        return $twig->render($template, $parameters);
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->getContainer()->get('kernel');
    }

    /**
     * @param string $bundle
     * @return BundleInterface|null
     */
    protected function getBundle($bundle)
    {
        try {
            $bundle = $this->getKernel()
                ->getBundle($bundle);
        } catch (\InvalidArgumentException $exception) {
            $bundle = null;
        }
        return $bundle;
    }

    /**
     * @return Validator
     */
    protected function getValidator()
    {
        return $this
            ->getContainer()
            ->get('odesskij_generator.validator');
    }

    /**
     * @param string $string
     * @return string
     */
    protected function underscore($string)
    {
        return Container::underscore($string);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function camelize($string)
    {
        return Container::camelize($string);
    }

    /**
     * Get the twig environment that will render skeletons
     *
     * @return \Twig_Environment
     */
    private function getTwigEnvironment()
    {
        $loader = new \Twig_Loader_Filesystem([]);
        $loader->addPath(__DIR__ . '/../Resources/skeleton', self::SKELETON_TWIG_NAMESPACE);
        $loader->addPath(__DIR__ . '/../Resources/suggest', self::SUGGEST_TWIG_NAMESPACE);
        $twig = new \Twig_Environment(
            $loader, [
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => true,
            'autoescape'       => false,
        ]);


        return $twig;
    }

    /**
     * @param $scope
     * @param $parameters
     * @return string
     */
    protected function getSuggest($scope, $parameters)
    {
        return $this->render(sprintf('@SUGGEST\%s.twig', $scope), $parameters);
    }
}