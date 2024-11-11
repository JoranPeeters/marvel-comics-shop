<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ComicRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private const COMICS_PER_PAGE = 20;

    public function __construct(
        private readonly ComicRepository $comicRepository
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $comics = $this->comicRepository->findAllWithPagination($page, SELF::COMICS_PER_PAGE);
        $totalPages = ceil(count($comics) / SELF::COMICS_PER_PAGE);

        return $this->render('home/index.html.twig', [
            'comics' => $comics,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}
