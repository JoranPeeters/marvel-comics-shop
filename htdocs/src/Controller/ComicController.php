<?php

namespace App\Controller;

use App\Repository\ComicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComicController extends AbstractController
{
    public function __construct(
        private readonly ComicRepository $comicRepository
    ) {}

    #[Route('/comic/{id}', name: 'app_comic_detail')]
    public function show(int $id): Response
    {
        $comic = $this->comicRepository->find($id);
    
        if (!$comic) {
            $this->addFlash('error', "No comic found with ID: {$id}");
            return $this->redirectToRoute('app_home');
        }
    
        return $this->render('comic/detail.html.twig', [
            'comic' => $comic,
        ]);
    }
}
