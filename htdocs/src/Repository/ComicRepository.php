<?php

namespace App\Repository;

use App\Entity\Comic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comic>
 */
class ComicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comic::class);
    }

    public function comicAdd(array $comicData): Void
    {
        $comic = new Comic();
        $comic->setMarvelId($comicData['marvel_id']);
        $comic->setTitle($comicData['title']);
        $comic->setDescription($comicData['description'] ?? 'No description available');
        $comic->setPageCount($comicData['page_count'] ?? 0);
        $comic->setDateOnSale(new \DateTime($comicData['date_on_sale'] ?? 'now'));
        $comic->setPrice($comicData['price'] ?? 0);
        $comic->setCreators($comicData['creators'] ?? 'Unknown creators');
        $comic->setThumbnail($comicData['thumbnail'] ?? 'No thumbnail available');

        $this->save($comic);
        $this->flush();
    }

    public function save(Comic $comic, bool $flush = false): void
    {
        $this->getEntityManager()->persist($comic);

        if ($flush) {
            $this->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Comic $comic): void
    {
        $this->getEntityManager()->remove($comic);
    }

    public function comicExists(int $marvelId): bool
    {
        return $this->findOneBy(['marvelId' => $marvelId]) !== null;
    }
}
