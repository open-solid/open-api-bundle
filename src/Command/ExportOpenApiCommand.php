<?php

declare(strict_types=1);

/*
 * This file is part of OpenSolid package.
 *
 * (c) Yonel Ceruto <open@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    description: 'Export the OpenAPI spec to a formatted file.',
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
        $io = new SymfonyStyle($input, $output);

        if (null === $openapi = $this->generator->generate()) {
            $io->error('OpenAPI spec not found.');

            return self::FAILURE;
        }

        $filename = $input->getOption('output');
        $openapi->saveAs($filename);

        $io->success('OpenAPI spec has been exported successfully.');

        return self::SUCCESS;
    }
}
