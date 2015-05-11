<?php
namespace Odesskij\Bundle\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateBundleCommand.
 *
 * @author Vladimir Odesskij <odesskij1992@gmail.com>
 */
class GenerateBundleCommand extends AbstractGeneratorCommand
{
    CONST TEMPLATE_DIR = '@SKELETON\bundle';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('odesskij:generate:bundle')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Bundle namespace')
            ->addArgument('name', InputArgument::REQUIRED, 'Bundle name')
            ->addOption('directory', 'd', InputOption::VALUE_OPTIONAL, 'Bundles Directory', 'src/');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $validator = $this->getValidator();
        $basedir = $this->getKernel()->getRootDir();
        $directory = $basedir . '/../' . $input->getOption('directory');

        $namespace = $validator->validateBundleNamespace(
            $input->getArgument('namespace')
        );

        $bundle = str_replace('\\', '', $input->getArgument('name'));
        $bundle = $validator->validateBundleName($bundle);

        if (!is_dir($directory)) {
            throw new \RuntimeException('Wrong target directory');
        }

        $directory .= '/' . strtr($namespace, '\\', '/');
        if (file_exists($directory)) {
            if (!is_dir($directory)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" exists but is a file.', realpath($directory)));
            }
            $files = scandir($directory);
            if ($files != ['.', '..']) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($directory)));
            }
            if (!is_writable($directory)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not writable.', realpath($directory)));
            }
        }

        $basename = substr($bundle, 0, -6);

        $parameters = [
            'namespace' => $namespace,
            'bundle' => $bundle,
            'bundle_basename' => $basename,
            'extension_alias' => $this->underscore($basename),
        ];

        $this->renderFile(static::TEMPLATE_DIR . '/Bundle.php.twig', $directory . '/' . $bundle . '.php', $parameters);
        $this->renderFile(static::TEMPLATE_DIR . '/Extension.php.twig', $directory . '/DependencyInjection/' . $basename . 'Extension.php', $parameters);
        $this->renderFile(static::TEMPLATE_DIR . '/Configuration.php.twig', $directory . '/DependencyInjection/Configuration.php', $parameters);
        $this->renderFile(static::TEMPLATE_DIR . '/DefaultController.php.twig', $directory . '/Controller/DefaultController.php', $parameters);
        $this->renderFile(static::TEMPLATE_DIR . '/index.html.twig.twig', $directory . '/Resources/views/Default/index.html.twig', $parameters);
        $this->renderFile(static::TEMPLATE_DIR . '/services.yml.twig', $directory . '/Resources/config/services.yml', $parameters);
        $this->renderFile(static::TEMPLATE_DIR . '/routing.yml.twig', $directory . '/Resources/config/routing.yml', $parameters);


        $output->writeln(sprintf('<info>%s</info>', $this->getSuggest('bundle', $parameters)));
    }
}
