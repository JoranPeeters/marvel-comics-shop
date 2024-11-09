<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ComicRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(ComicRepository $comicRepository, Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $comicsPerPage = 20;

        $paginator = $comicRepository->findAllWithPagination($page, $comicsPerPage);
        $totalPages = ceil(count($paginator) / $comicsPerPage);

        return $this->render('home/index.html.twig', [
            'comics' => $paginator,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}
