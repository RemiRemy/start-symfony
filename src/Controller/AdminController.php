<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(ProduitRepository $repo): Response
    {
        $listeProduit = $repo->findAll();

        return $this->render('admin/index.html.twig', [
            'listeProduit' => $listeProduit
        ]);
    }
}


    //créer la méthode permettant de supprimer un produit
    //
    //la route s'appellera /admin/supression-produit/42
    //
    //$repo->delete(42)
    //
    //rediriger vers /admin

    #[Route('/admin/suppression-produit/{id}', name: 'suppression_produit')]

    public function suppressionProduit($id, ProduitRepository $repo): Response
    {
        $repo->delete($id);

        return $this->redirect('/admin');
       
    }