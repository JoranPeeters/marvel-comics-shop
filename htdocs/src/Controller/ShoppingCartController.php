<?php

namespace App\Controller;

use App\Repository\ComicRepository;
use App\Repository\ShoppingCartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/shopping-cart')]
class ShoppingCartController extends AbstractController
{
    public function __construct(
        private readonly ShoppingCartRepository $shoppingCartRepository,
        private readonly ComicRepository $comicRepository
    ) {}

    #[Route('/', name: 'app_shopping_cart_index')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(): Response
    {
        $user = $this->getUser();
        $cartItems = $this->shoppingCartRepository->findBy(['user' => $user, 'deletedAt' => null]);

        return $this->render('shopping_cart/index.html.twig', [
            'cartItems' => $cartItems,
        ]);
    }

    #[Route('/add/{comicId}', name: 'app_shopping_cart_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(int $comicId, Request $request): Response
    {
        $user = $this->getUser();
        $quantity = $request->query->getInt('quantity', 1);
        $comic = $this->comicRepository->find($comicId);

        if (!$comic) {
            throw $this->createNotFoundException('Comic not found.');
        }

        $cartItem = $this->shoppingCartRepository->addComicToCart($user, $comic, $quantity);
        $this->addFlash('success', "{$comic->getTitle()} has been added to your shopping cart.");

        return $this->redirectToRoute('app_shopping_cart_index');
    }

    #[Route('/remove/{comicId}', name: 'app_shopping_cart_remove')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(int $comicId): Response
    {
        $user = $this->getUser();
        $cartItem = $this->shoppingCartRepository->findOneBy(['user' => $user, 'comic' => $comicId, 'deletedAt' => null]);

        if (!$cartItem) {
            throw $this->createNotFoundException('Comic not found in your shopping cart.');
        }

        $this->shoppingCartRepository->remove($cartItem);
        $this->addFlash('success', "{$cartItem->getComic()->getTitle()} has been removed from your shopping cart.");

        return $this->redirectToRoute('app_shopping_cart_index');
    }
}
