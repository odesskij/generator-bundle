<?php
namespace Odesskij\Bundle\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * GenerateCommandCommand.
 *
 * @author Vladimir Odesskij <odesskij1992@gmail.com>
 */
class GenerateCommandCommand extends AbstractGeneratorCommand
{
    CONST TEMPLATE_DIR = '@SKELETON\command';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('odesskij:generate:command')
            ->addArgument('bundle', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED);
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

        $command = $input->getArgument('name');

        $directory = $bundle->getPath() . '/Command';
        $file = $directory . '/' . $command . 'Command.php';
        if (file_exists($file)) {
            throw new \RuntimeException(sprintf('File "%s" already exists', $file));
        }

        $vendor = explode('\\', $bundle->getNamespace(), 2);
        $vendor = $this->underscore($vendor[0]);
        $alias = str_replace('_', ':', $this->underscore($command));
        $parameters = [
            'namespace' => $bundle->getNamespace(),
            'vendor' => $vendor,
            'alias' => $alias,
            'name' => $vendor . ':' . $alias,
            'command' => $command
        ];

        $this->renderFile(self::TEMPLATE_DIR . '/Command.php.twig', $file, $parameters);

        $output->writeln(sprintf('<info>%s</info>', $this->getSuggest('command', $parameters)));
    }
}