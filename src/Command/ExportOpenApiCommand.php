<?php

namespace OpenSolid\OpenApiBundle\Command;

use OpenSolid\OpenApiBundle\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'openapi:export',
    description: 'Export the OpenAPI documentation to a formatted file.',
)]
class ExportOpenApiCommand extends Command
{
    public function __construct(private readonly Generator $generator)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'The output file path.', 'openapi.yaml', ['openapi.yaml', 'openapi.json'])
            ->addUsage('openapi.yaml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $input->getOption('output');

        $this->generator->generate()
            ->saveAs($filename)
        ;

        $io = new SymfonyStyle($input, $output);
        $io->success('OpenAPI documentation exported successfully.');

        return self::SUCCESS;
    }
}