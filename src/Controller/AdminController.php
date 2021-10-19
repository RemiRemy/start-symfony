<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Slide;
use App\Repository\ProduitRepository;
use App\Repository\SlideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(ProduitRepository $repo, SlideRepository $repoSlide): Response
    {
        $listeProduit = $repo->findAll();

        $detailSlide = $repoSlide->findAll();

        return $this->render('admin/index.html.twig', [
            'listeProduit' => $listeProduit,
            "detailSlide" => $detailSlide
        ]);
    }



    //créer la méthode permettant de supprimer un produit
    //
    //la route s'appellera /admin/supression-produit/42
    //
    //$repo->delete(42)
    //
    //rediriger vers /admin

    #[Route('/admin/suppression-produit/{id}', name: 'suppression_produit')]
    public function suppressionProduit($id, EntityManagerInterface $manager): Response
    {
        // ici on créait un objet produit possédant le bon id afin que le manager comprenne ce qu'il doit supprimer 
        // (par exemple une ligne de la table produit ayant pour clé primaire "#id")
        $produit = $manager->getReference('App\\Entity\\Produit', $id);
        $manager->remove($produit);
        $manager->flush();

        return $this->redirect('/admin');
    }



    #[Route('/admin/creation-produit', name: 'creation_produit')]
    #[Route('/admin/edition-produit/{id}', name: 'edition_produit')]
    public function editionProduit(Produit $produit = null, Request $request, EntityManagerInterface $manager): Response
    {
        if ($produit == null) {
            $produit = new Produit();
        }

        $formulaire = $this->createFormBuilder($produit)
            // permet de créer le formulaire pour gagner du temps 
            ->add(
                'designation',
                TextType::class, // on peut mettre null 
                [
                    'label' => 'Désignation',
                    'attr' => [
                        'placeholder' => 'Nom du produit',
                        'class' => 'form-control', // permet de mettre une classe CSS
                    ],
                    'row_attr' => ['class' => 'form-group'],
                ]
            )
            ->add(
                'description',
                TextareaType::class, // on peut mettre null 
                [
                    'attr' => [
                        'placeholder' => 'Description du produit',
                        'class' => 'form-control', // permet de mettre une classe CSS
                    ],
                    'row_attr' => ['class' => 'form-group'],
                ]
            )
            ->add(
                'prix',
                null, // on peut mettre null 
                [
                    'attr' => [
                        'placeholder' => 'Prix TTC du produit',
                        'class' => 'form-control', // permet de mettre une classe CSS
                    ],
                    'row_attr' => ['class' => 'form-group'],
                ]
            )
            ->add('nomImage', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control', // permet de mettre une classe CSS
                ],
                'constraints' => [
                    new File(
                        [
                            'mimeTypes' => ['image/jpeg', 'image/png'],
                            'mimeTypesMessage' => "Format jpg ou png uniquement"
                        ]
                    )
                ]
            ])
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Enregistrer',
                    'attr' => ['class' => 'btn btn-success']
                ]
            )
            ->getForm();

        // On récupère les données de la requête (du formulaire) et on les affectes au formulaire (et donc à $produit)
        $formulaire->handleRequest($request);

        // uniquement si l'utilisateur a cliqué sur le bouton enregistrer
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {

            //on récupère l'image qui a été choisi par l'utilisateur
            $image = $formulaire->get("nomImage")->getData();

            // si l'utilisateur a selectionné un fichier
            if ($image) {
                $nomOriginal = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);


                // $nomUnique = $nomOriginal . "-" . time();
                $nomUnique = $nomOriginal . "-" . uniqid() . '.' . $image->guessExtension();  // permet de gérer l'extension des photo jpg png ...

                $image->move("uploads", $nomUnique);

                $produit->setNomImage($nomUnique);
            }

            $manager->persist($produit);
            $manager->flush();
            return $this->redirect('/admin');
        }

        $vueFormulaire = $formulaire->createView();



        return $this->render('admin/edition-produit.html.twig', [
            'produit' => $produit,
            'vueFormulaire' => $vueFormulaire
        ]);
    }

    // ***************************************************************** CAROUSEL *****************************************************************************


    #[Route('/admin/creation-carousel', name: 'creation-carousel')]
    #[Route('/admin/modif-carousel/{id}', name: 'modif-carousel')]
    public function modifCarousel(Slide $slide = null, Request $request, EntityManagerInterface $manager): Response
    {

        if ($slide == null) {
            $slide = new Slide();
        }

        $formulaire = $this->createFormBuilder($slide)
            // permet de créer le formulaire pour gagner du temps 
            ->add(
                'titre',
                TextType::class, // on peut mettre null 
                [
                    // 'label' => 'Titre',
                    'attr' => [
                        'placeholder' => 'Titre Carousel',
                        'class' => 'form-control', // permet de mettre une classe CSS
                    ],
                    'row_attr' => ['class' => 'form-group'],
                ]
            )
            ->add(
                'texte',
                TextareaType::class, // on peut mettre null 
                [
                    'attr' => [
                        'placeholder' => 'Slogan du carousel',
                        'class' => 'form-control', // permet de mettre une classe CSS
                    ],
                    'row_attr' => ['class' => 'form-group'],
                ]
            )

            ->add(
                'nomImage',
                FileType::class,
                [
                    'label' => 'Image',
                    'mapped' => false,
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control', // permet de mettre une classe CSS
                    ],
                    'constraints' => [
                        new File(
                            [
                                'mimeTypes' => ['image/jpeg', 'image/png'],
                                'mimeTypesMessage' => "Format jpg ou png uniquement"
                            ]
                        )
                    ]
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Enregistrer',
                    'attr' => ['class' => 'btn btn-success']
                ]
            )
            ->getForm();


        // On récupère les données de la requête (du formulaire) et on les affectes au formulaire (et donc à $slide)
        $formulaire->handleRequest($request);

        // uniquement si l'utilisateur a cliqué sur le bouton enregistrer
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {

            //on récupère l'image qui a été choisi par l'utilisateur
            $image = $formulaire->get("nomImage")->getData();

            // si l'utilisateur a selectionné un fichier
            if ($image) {
                $nomOriginal = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);


                // $nomUnique = $nomOriginal . "-" . time();
                $nomUnique = $nomOriginal . "-" . uniqid() . '.' . $image->guessExtension();  // permet de gérer l'extension des photo jpg png ...

                $image->move("uploads", $nomUnique);

                $slide->setNomImage($nomUnique);
            }

            $manager->persist($slide);
            $manager->flush();
            return $this->redirect('/admin');
        }

        $vueFormulaire = $formulaire->createView();



        return $this->render('admin/modif-carousel.html.twig', [
            'slide' => $slide,
            'vueFormulaire' => $vueFormulaire
        ]);
    }



    #[Route('/admin/suppression-slide/{id}', name: 'suppression_slide')]
    public function suppressionSlide($id, EntityManagerInterface $manager): Response
    {
        // ici on créait un objet slide possédant le bon id afin que le manager comprenne ce qu'il doit supprimer 
        // (par exemple une ligne de la table slide ayant pour clé primaire "#id")
        $slide = $manager->getReference('App\\Entity\\Slide', $id);
        $manager->remove($slide);
        $manager->flush();

        return $this->redirect('/admin');
    }
}
