<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier')]
    public function index(Request $request, ProduitRepository $repo): Response
    {

        $session = $request->getSession();
        $panier = $session->get('panier', []);

        $detailPanier = [];
        $total = 0;
        $nombreProduit = 0;

        foreach ($panier as $idProduit => $quantite) {

            $nombreProduit += $quantite;

            $produit = $repo->find($idProduit);

            $total += $produit->getPrix() * $quantite;

            $detailPanier[] = [
                'produit' => $repo->find($idProduit),
                'quantite' => $quantite,
            ];
        }

        return $this->render('panier/index.html.twig', [
            'detailPanier' => $detailPanier,
            'total' => $total,
            'nombreProduit' => $nombreProduit
        ]);
    }

    #[Route('/ajout-panier/{id}', name: 'ajout_panier')]
    public function ajoutPanier($id, Request $request): Response
    {

        // récupère la session ( pour récupérer le panier)
        $session = $request->getSession();

        // On récupère le panier et si il n'éxiste pas: un tableau vide
        $panier = $session->get('panier', []);

        // si il y a déjà cet article dans le panier, on augmente de 1 la quantité 
        if (isset($panier[$id])) {
            $panier[$id]++;
            // sinon on ajoute l'id de l'article au panier avec une quantité de 1    
        } else {
            $panier[$id] = 1;
        }

        // On obtient par exemple un tableau de ce genre [42=>5 , 19=>3 , 33=>1]
        // (ex : l'article avec l'id 42 à une quantité de 5 dans le panier )

        // On sevegarde le panier dans la session 
        $session->set('panier', $panier);

        // dd($panier);

        // redirection vers la page panier 
        return $this->redirect('/panier');
    }
}
