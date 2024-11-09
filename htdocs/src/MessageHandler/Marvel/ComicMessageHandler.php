<?php

namespace App\MessageHandler\Marvel;

use App\Message\Marvel\ComicMessage;
use App\Repository\ComicRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ComicMessageHandler
{
    public function __construct(
        private ComicRepository $comicRepository,
        private LoggerInterface $logger
    ) {}

    public function __invoke(ComicMessage $comicData)
    {
        $comic = json_decode($comicData->getComicData(), true);
        
        if ($this->comicRepository->comicExists($comic['marvel_id'])) {
            $this->logger->info("Comic with Marvel ID {$comic['marvel_id']} already exists. Skipping.");
            return;
        }

        $this->comicRepository->comicAdd($comic);
        $this->logger->info("Comic with Marvel ID {$comic['marvel_id']} added successfully.");
    }
}
