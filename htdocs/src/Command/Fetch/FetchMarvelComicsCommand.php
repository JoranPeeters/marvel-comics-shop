<?php

namespace App\Command\Fetch;

use App\Service\Marvel\MarvelComicService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fetch:marvel-comics',
    description: 'Fetch and process Marvel comics data using the Marvel API',
)]
class FetchMarvelComicsCommand extends Command
{
    private const COMIC_FETCH_LIMIT = 150;

    public function __construct(
        private readonly MarvelComicService $marvelComicService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('This command will fetch and process Marvel comics from the Marvel API and create a message for each comic');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $processedComicCount = $this->marvelComicService->processComics(self::COMIC_FETCH_LIMIT);

        if ($processedComicCount === 0) {
            return $this->outputFailed($output);
        }

        $output->writeln('<fg=green>Total Comics Processed: ' . $processedComicCount . '</>');

        return $this->outputSuccess($output);
    }

    private function outputFailed(OutputInterface $output): int
    {
        $output->writeln('<fg=red>No Comics Found!</>');
        return Command::FAILURE;
    }

    private function outputSuccess(OutputInterface $output): int
    {
        $output->writeln('<fg=green>Comics successfully imported!</>');
        return Command::SUCCESS;
    }
}