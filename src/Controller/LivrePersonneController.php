<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Livre;
use App\Entity\LivrePersonne;
use App\Form\LivrePersonneType;
use Symfony\Component\HttpFoundation\Request;

class LivrePersonneController extends AppController
{
    /**
     * @Route("/livre_personne", name="livre_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     */
    public function index()
    {
        return $this->render('livre_personne/index.html.twig', [
            'controller_name' => 'LivrePersonneController',
        ]);
    }
    
    /**
     * Affichage des propriétaires d'un livre
     *
     * @Route("/livre_personne/{slug}/afficher_pour_livre", name="personne_pour_livre")
     *
     * @param Livre $livre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherProprios(Livre $livre){
        return $this->render('livre_personne/afficher_proprios_livre.html.twig', array(
            'livre' => $livre,
            'livrePersonnes' => $livre->getLivrePersonnes()
        ));
    }
    
    /**
     * Ajout d'un propriétaire à un livre
     *
     * @Route("/livre_personne/{slug}/ajax_ajouter_personne", name="ajax_livre_personne_ajouter_personne")
     *
     * @param Request $request
     * @param Livre $livre
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouterDepuisLivre(Request $request, Livre $livre){
        $livrePersonne = new LivrePersonne();
        $livrePersonne->setLivre($livre);
        
        $form = $this->createForm(LivrePersonneType::class, $livrePersonne, array(
            'avec_livre' => false
        ));
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $livrePersonne = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($livrePersonne);
            $manager->flush();
            
            return $this->json(array(
                'statut' => true
            ));
        }
        
        return $this->render('livre_personne/ajax_ajouter_personne.html.twig', array(
            'form' => $form->createView(),
            'livre' => $livre
        ));
    }
    
    /**
     * Edition lien personne - livre depuis livre
     *
     * @Route("/livre_personne/{id}/ajax_editer_personne", name="ajax_livre_personne_editer_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param Livre $livre
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editerDepuisLivre(Request $request, LivrePersonne $livrePersonne){
        
        $form = $this->createForm(LivrePersonneType::class, $livrePersonne, array(
            'avec_livre' => false
        ));
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $livrePersonne = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($livrePersonne);
            $manager->flush();
            
            return $this->json(array(
                'statut' => true
            ));
        }
        
        return $this->render('livre_personne/ajax_editer_personne.html.twig', array(
            'form' => $form->createView(),
            'livrePersonne' => $livrePersonne
        ));
    }
    
    /**
     * Suppression lien personne - livre depuis livre
     *
     * @Route("/livre_personne/{id}/ajax_supprimer_personne", name="ajax_livre_personne_supprimer_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param LivrePersonne $livrePersonne
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function supprimerDepuisLivre(LivrePersonne $livrePersonne){
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($livrePersonne);
        $manager->flush();
        
        return $this->json(array(
            'statut' => true
        ));
    }
}
