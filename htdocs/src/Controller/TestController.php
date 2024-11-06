<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TestController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        //$this->addFlash('success', $this->translator->trans('message.roadtrip.created'));
        return $this->render('home/test.html.twig', []);
    }
}
