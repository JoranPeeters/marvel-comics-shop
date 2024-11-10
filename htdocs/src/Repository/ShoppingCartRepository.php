<?php

namespace App\Repository;

use App\Entity\ShoppingCart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShoppingCart>
 */
class ShoppingCartRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingCart::class);
    }

    public function save(ShoppingCart $shoppingCart, bool $flush = false): void
    {
        $this->getEntityManager()->persist($shoppingCart);

        if ($flush) {
            $this->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function addComicToCart($user, $comic, $quantity = 1): ShoppingCart
    {
        $cartItem = $this->findOneBy(['user' => $user, 'comic' => $comic, 'deletedAt' => null]);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new ShoppingCart();
            $cartItem->setUser($user);
            $cartItem->setComic($comic);
            $cartItem->setQuantity($quantity);
        }

        $this->save($cartItem);
        $this->flush();

        return $cartItem;
    }

    public function remove(ShoppingCart $cartItem): void
    {
        $cartItem->setDeletedAt(new \DateTime());
        $this->flush();
    }
}
