<?php

namespace App\Message\Marvel;

class ComicMessage
{
    public function __construct(
        private string $comicData
    ) {}

    public function getComicData(): string
    {
        return $this->comicData;
    }
}
