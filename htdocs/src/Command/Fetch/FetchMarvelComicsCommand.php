<?php

namespace App\Command\Fetch;

use App\Service\Marvel\MarvelComicService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fetch:marvel-comics',
    description: 'Import data using the Marvel API',
)]
class FetchMarvelComicsCommand extends Command
{
    public function __construct(
        private readonly MarvelComicService $marvelComicService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('This command will import Marvel comics from marvel api');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Fetch comics from the Marvel API
        $allComics = $this->marvelComicService->fetchComics(500);

        if (empty($allComics)) {
            return $this->outputFailed($output);
        }

        foreach ($allComics as $comic) {
            // TODO: reate a message for each comic
            $output->writeln('ID: ' . $comic['id'] . ' | Title: ' . $comic['title'] . ' | Thumbnail: ' . $comic['thumbnail']['path'] . '.' . $comic['thumbnail']['extension']);
        }

        $output->writeln('<fg=green>Total Comics Found: ' . count($allComics) . '</>');

        return $this->outputSuccess($output);
    }

    private function outputFailed($output): int
    {
        $output->writeln([
            '<fg=red>No Comics Found!</>',
            '<fg=red>=============================================</>',
            '<fg=red> /$$$$$$$$       /$$ /$$                 /$$</>',
            '<fg=red>| $$_____/      |__/| $$                | $$</>',
            '<fg=red>| $$    /$$$$$$  /$$| $$  /$$$$$$   /$$$$$$$</>',
            '<fg=red>| $$$$$|____  $$| $$| $$ /$$__  $$ /$$__  $$</>',
            '<fg=red>| $$__/ /$$$$$$$| $$| $$| $$$$$$$$| $$  | $$</>',
            '<fg=red>| $$   /$$__  $$| $$| $$| $$_____/| $$  | $$</>',
            '<fg=red>| $$  |  $$$$$$$| $$| $$|  $$$$$$$|  $$$$$$$</>',
            '<fg=red>|__/   \_______/|__/|__/ \_______/ \_______/</>',
        ]);

        return Command::FAILURE;
    }

    private function outputSuccess($output): int
    {
        $output->writeln([
            '<fg=green>Comics successfully imported!</>',
            '<fg=green>=======================================================================</>',
            '<fg=green> /$$$$$$</>',
            '<fg=green>/$$__  $$</>',                                                          
            '<fg=green>| $$  \__/ /$$   /$$  /$$$$$$$  /$$$$$$$  /$$$$$$   /$$$$$$$ /$$$$$$$</>',
            '<fg=green>|  $$$$$$ | $$  | $$ /$$_____/ /$$_____/ /$$__  $$ /$$_____//$$_____</>',
            '<fg=green>\____  $$| $$  | $$| $$      | $$      | $$$$$$$$|  $$$$$$|  $$$$$$</>', 
            '<fg=green>/$$  \ $$| $$  | $$| $$      | $$      | $$_____/ \____  $$\____  $$</>',
            '<fg=green>|  $$$$$$/|  $$$$$$/|  $$$$$$$|  $$$$$$$|  $$$$$$$ /$$$$$$$//$$$$$$$/</>',
            '<fg=green>\______/  \______/  \_______/ \_______/ \_______/|_______/|_______/</>',
        ]);

        return Command::SUCCESS;
    }
}