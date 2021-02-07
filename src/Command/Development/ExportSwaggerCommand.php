<?php

namespace App\Command\Development;

use Nelmio\ApiDocBundle\ApiDocGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command run before tests.
 *
 * @see tests/bootstrap.php
 */
class ExportSwaggerCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'api:swagger:export';

    private string $kernelProjectDir;

    private ApiDocGenerator $apiDocGenerator;

    public function __construct(string $kernelProjectDir, ApiDocGenerator $apiDocGenerator)
    {
        parent::__construct();

        $this->kernelProjectDir = $kernelProjectDir;
        $this->apiDocGenerator = $apiDocGenerator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addArgument('output_path', InputArgument::OPTIONAL, 'The output path of the swagger.yml file', 'var/cache/')
            ->setDescription('Exports a swagger file for the API')
            ->setHelp('This command allows you to export the swagger definition json file of the API');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $spec = $this->apiDocGenerator->generate()->toJson();

        $swaggerFile = sprintf('%s/%sswagger.json', $this->kernelProjectDir, $input->getArgument('output_path'));

        $io->writeln(
            sprintf('Writing file <info>%s</info>...', $swaggerFile)
        );

        file_put_contents($swaggerFile, $spec);

        return Command::SUCCESS;
    }
}
