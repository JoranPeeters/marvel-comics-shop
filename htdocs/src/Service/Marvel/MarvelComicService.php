<?php

namespace App\Service\Marvel;
use App\Service\Logger\LoggerService;
use App\Message\Marvel\ComicMessage;
use Symfony\Component\Messenger\MessageBusInterface;


class MarvelComicService
{
    private const COMIC_FETCH_BATCH_SIZE = 10; // Maximum of 50 comics per batch

    public function __construct(
        private readonly MarvelApiService $marvelApiService,
        private readonly LoggerService $loggerService,
        private readonly MessageBusInterface $messageBus
    )
    {}

    public function processComics(int $totalComics): int
    {
        $comics = $this->fetchComics($totalComics);
        $this->createComicMessage($comics);
        
        return count($comics);
    }

    private function fetchComics(int $totalComics): array
    {
        $offset = 0;
        $allComics = [];
        $limit = $totalComics < self::COMIC_FETCH_BATCH_SIZE ? $totalComics : self::COMIC_FETCH_BATCH_SIZE;

        $queryParams = [
            'format' => 'comic',
            'formatType' => 'comic',
            'orderBy' => 'modified',
            'startYear' => 2014,
            'noVariants' => true
        ];

        do {
            $queryParams['offset'] = $offset;
            $queryParams['limit'] = min($limit, $totalComics - $offset);
            $comics = $this->marvelApiService->marvelApiCall('comics', $queryParams);

            if ($comics === null) {
                $this->loggerService->logWarning('Skipped comics at offset ' . $offset . ' due to API error');
                $offset += $limit;
                continue;
            }

            if (empty($comics['data']['results'])) {
                $this->loggerService->logWarning('No comics found at offset ' . $offset);
                $offset += $limit;
                continue;
            }

            $allComics = array_merge($allComics, $comics['data']['results']);
            $this->loggerService->logInfo('Successfully fetched ' . count($comics['data']['results']) . ' comics from Marvel API');

            $offset += $limit;
            usleep(50000);

        } while($offset < $totalComics);

        return $allComics;
    }

    private function formatComicData(array $comic): array
    {
        $formattedComic = [
            'marvel_id' => $comic['id'],
            'title' => $comic['title'] ?? 'No title available',
            'description' => empty($comic['description']) ? 'No description available' : $comic['description'],
            'page_count' => $comic['pageCount'] ?? 0,
            'date_on_sale' => $this->getDate($comic['dates'], 'onsaleDate'),
            'price' => $comic['prices'][0]['price'] ?? 0,
            'creators' => $this->getCreators($comic['creators']),
            'thumbnail' => $comic['thumbnail']['path'] . '.' . $comic['thumbnail']['extension']
        ];
    
        return $formattedComic;
    }

    private function getDate(array $dates, string $type): ?string
    {
        foreach ($dates as $date) {
            if ($date['type'] === $type) {
                return $date['date'];
            }
        }

        return null;
    }

    private function getCreators(array $creators): string
    {
        if (($creators['available']) === 0 || empty($creators['items'])) {
            return 'Not defined';
        }
    
        $creatorNames = [];

        if (is_array($creators['items'])) {
            foreach ($creators['items'] as $creator) {
                $creatorNames[] = $creator['name'] . ' (' . $creator['role'] . ')';
            }
        }
    
        return implode(', ', $creatorNames);
    }

    private function createComicMessage(array $comics): void
    {
        foreach ($comics as $comic) {
            $formattedComic = $this->formatComicData($comic);
        
            if ($formattedComic['price'] <= 0) {
                $this->loggerService->logInfo('Comic skipped due to missing price: ' . $formattedComic['title']);
                continue;
            } 
            
            $this->messageBus->dispatch(new ComicMessage(json_encode($formattedComic)));
        }  
    }
}
