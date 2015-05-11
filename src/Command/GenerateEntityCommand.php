<?php
namespace Odesskij\Bundle\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * GenerateEntityCommand.
 *
 * @author Vladimir Odesskij <odesskij1992@gmail.com>
 */
class GenerateEntityCommand extends AbstractGeneratorCommand
{
    CONST TEMPLATE_DIR = '@SKELETON\entity';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('odesskij:generate:entity')
            ->addArgument('bundle', InputArgument::REQUIRED)
            ->addArgument('entity', InputArgument::REQUIRED)
            ->addOption('with-repository', 'wr', InputOption::VALUE_OPTIONAL, 'Generate entity repository class', true)
            ->addOption('with-repository-interface', 'wri', InputOption::VALUE_OPTIONAL, 'Generate entity repository interface', false);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundle = $this->getBundle($input->getArgument('bundle'));

        if (!$bundle instanceof BundleInterface) {
            throw new \RuntimeException(
                sprintf('Bundle "%s" is not found.', $input->getArgument('bundle'))
            );
        }

        $entity = $input->getArgument('entity');

        $directory = $bundle->getPath() . '/Entity';

        foreach ([$entity . '.php', $entity . 'Repository.php', $entity . 'RepositoryInterface.php'] as $file) {
            $f = $directory . '/' . $file;
            if (file_exists($f)) {
                throw new \RuntimeException(sprintf('File "%s" already exists', $f));
            }
        }

        $basename = substr($bundle->getName(), 0, -6);
        $parameters = [
            'namespace' => $bundle->getNamespace(),
            'entity' => $entity,
            'bundle' => $bundle->getName(),
            'bundle_basename' => $basename,
            'bundle_alias' => $this->underscore($basename),
            'entity_alias' => $this->underscore($entity)
        ];


        if ($input->getOption('with-repository')) {
            $parameters['entityRepository'] = $entity . 'Repository';
            if ($input->getOption('with-repository-interface')) {
                $parameters['entityRepositoryInterface'] = $entity . 'RepositoryInterface';
            }
        }


        $this->renderFile(static::TEMPLATE_DIR . '/Entity.php.twig', $directory . '/' . $entity . '.php', $parameters);

        if ($input->getOption('with-repository')) {
            $this->renderFile(static::TEMPLATE_DIR . '/Repository.php.twig', $directory . '/' . $entity . 'Repository.php', $parameters);
            if ($input->getOption('with-repository-interface')) {
                $this->renderFile(static::TEMPLATE_DIR . '/RepositoryInterface.php.twig', $directory . '/' . $entity . 'RepositoryInterface.php', $parameters);
            }
        }

        $output->writeln(sprintf('<info>%s</info>', $this->getSuggest('entity', $parameters)));
    }
}
