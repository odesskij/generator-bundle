<?php
namespace Odesskij\Bundle\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * GenerateFixturesCommand.
 *
 * @author Vladimir Odesskij <odesskij1992@gmail.com>
 */
class GenerateFixturesCommand extends AbstractGeneratorCommand
{
    CONST TEMPLATE_DIR = '@SKELETON\fixtures';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('odesskij:generate:fixtures')
            ->addArgument('bundle', InputArgument::REQUIRED)
            ->addArgument('fixtures', InputArgument::REQUIRED);
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

        $fixtures = $input->getArgument('fixtures');
        $fixturesClass = 'Load' . $fixtures . 'Data';

        $directory = $bundle->getPath() . '/DataFixtures/ORM';

        foreach ([$fixturesClass . '.php'] as $file) {
            $f = $directory . '/' . $file;
            if (file_exists($f)) {
                throw new \RuntimeException(sprintf('File "%s" already exists', $f));
            }
        }

        $basename = substr($bundle->getName(), 0, -6);
        $parameters = [
            'namespace'       => $bundle->getNamespace() . '\DataFixtures\ORM',
            'fixtures'        => $fixturesClass,
            'bundle'          => $bundle->getName(),
            'bundle_basename' => $basename,
            'bundle_alias'    => $this->underscore($basename),
            'fixtures_alias'  => $this->underscore($fixtures)
        ];


        $this->renderFile(static::TEMPLATE_DIR . '\LoadData.php.twig', $directory . '/' . $fixturesClass . '.php', $parameters);

        $output->writeln(sprintf('<info>%s</info>', $this->getSuggest('fixtures', $parameters)));
    }
}
