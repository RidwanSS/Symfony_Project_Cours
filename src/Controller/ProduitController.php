<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Form\ProduitType; // app = src
use Symfony\Component\HttpFoundation\Request; //symfony est dans vendor

class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(Request $request)
    {
        //Récupérer Doctrine (service de gestion de BDD)
        $pdo = $this -> getDoctrine()->getManager();
        
        $produit = new Produit();
        //Création d'un form
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                //Le formulaire a été envoyé, on le sauvegarde
                $pdo->persist($produit);   //prepare
                $pdo->flush();             //execute
            }

        //Récuperer tous les produits
        $produits = $pdo -> getRepository(Produit::class)->findAll();

        /*
            ->findAll() pour tout récuperer tout ce qu'il y a dans la class produit
            ->findOneBy(['id' => 2])
            ->findBy(['nom' => 'Nom du produit])
         */
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'form_produit_new' => $form->createView()
            // "=>" pour un tableau associatif
            // "->" pour un objet
        ]);
    }

    /**
     * @Route("/produit/{id}", name="mon_produit")
     */
    public function produit(Request $request, Produit $produit=null){ //création d'un méthode

        if($produit !=null){ // if pour si on met "produit/10" dans URL cela va ramener a la page accueil car pas de produit 10
            $form = $this->createForm(ProduitType::class, $produit);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $pdo = $this -> getDoctrine()->getManager();
                $pdo->persist($produit);
                $pdo->flush();
            }
            
            return $this->render('produit/produit.html.twig',[
                'produit'=> $produit,
                'form'=>$form->createView()
            ]);

        }
        else{
            $this-> addFlash("danger", "Catégorie introuvable");
            return $this->redirectToRoute('accueil');
        }
        

    }

    /**
     * @Route("/produit/delete/{id}", name="delete_produit")
     */
    public function delete(Produit $produit=null){ // methode pour supprimer un produit 
        if($produit != null){
            $pdo = $this->getDoctrine()->getManager();
            $pdo -> remove($produit); //Suppression;
            $pdo -> flush();

            $this-> addFlash("success", "Produit supprimé"); //message flash comme les alert
        }
        else{
            $this-> addFlash("danger", "Produit introuvable");
        }

        return $this->redirectToRoute('accueil');
    }
}
