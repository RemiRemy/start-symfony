<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\SlideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(
        ProduitRepository $repo,
        SlideRepository $repoSlide
    ): Response {
        $listeProduits = $repo->findAllJoinLibelle();

        $detailSlide = $repoSlide->findAll();

        return $this->render('accueil/index.html.twig', ["listeProduits" => $listeProduits, "detailSlide" => $detailSlide]);
    }
}
