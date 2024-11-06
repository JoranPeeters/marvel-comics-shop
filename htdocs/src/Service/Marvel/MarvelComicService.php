<?php

namespace App\Service\Marvel;

class MarvelComicService
{
    private $comicsPerPage = 50;

    public function __construct(
        private readonly MarvelApiService $marvelApiService,
    )
    {}

    public function fetchComics(int $totalComics): array
    {
        $offset = 0;
        $allComics = [];
        $limit = $totalComics < $this->comicsPerPage ? $totalComics : $this->comicsPerPage;

        $queryParams = [
            'format' => 'comic',
            'formatType' => 'comic',
            'orderBy' => 'issueNumber',
        ];

        do {
            $queryParams['offset'] = $offset;
            $queryParams['limit'] = min($limit, $totalComics - $offset);

            $comics = $this->marvelApiService->marvelApiCall('comics', $queryParams);

            if (!empty($comics['data']['results'])) {
                $allComics = array_merge($allComics, $comics['data']['results']);
            }

            $offset += $limit;
            usleep(50000);

        } while($offset < $totalComics);

        return $allComics;
    }
}
