<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use App\Form\CategorieType; // app = src
use Symfony\Component\HttpFoundation\Request; //symfony est dans vendor

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie", name="categorie")
     */
    public function index(Request $request)
    {
        $pdo= $this->getDoctrine()->getManager();

        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                //Le formulaire a été envoyé, on le sauvegarde
                $pdo->persist($categorie);   //prepare
                $pdo->flush();             //execute
            }
        
        $categories = $pdo -> getRepository(Categorie::class)->findAll();

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
            'form_categorie_new' => $form->createView()
        ]);
    }

    /**
     * @Route("/categorie/{id}", name="ma_categorie")
     */
    public function categorie(Request $request, Categorie $categorie=null){

        if($categorie !=null){ // if pour si on met "categorie/10" dans URL cela va ramener a la page accueil car pas de categorie 10
            $form = $this->createForm(CategorieType::class, $categorie);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $pdo = $this -> getDoctrine()->getManager();
                $pdo-> persist($categorie);
                $pdo->flush();
            }
            return $this->render('categorie/categorie.html.twig',[
                'categorie'=> $categorie,
                'form'=>$form->createView()
            ]);
        }
        else{
            $this-> addFlash("danger", "Catégorie introuvable");
            return $this->redirectToRoute('categorie');
        }


    }

    /**
     * @Route("/categorie/delete/{id}", name="delete_categorie")
     */
    public function delete(Categorie $categorie=null){ // methode pour supprimer une categorie
        if($categorie != null){
            $pdo = $this->getDoctrine()->getManager();
            $pdo -> remove($categorie); //Suppression
            $pdo -> flush(); 

            $this -> addFlash("success", "Catégorie supprimée"); //message flash comme les alert
        }
        else{
            $this-> addFlash("danger", "Catégorie introuvable");
        }

        //Permet de retourner à la page d'accueil de categorie
            return $this->redirectToRoute('categorie');
        
    }
}
