<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Acteur;
use App\Form\ActeurType;

class ActeurController extends AppController
{
    /**
     * Liste des acteurs
     *
     * @Route("/acteur/liste/{page}", name="acteur_liste")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Acteur::class);
        
        $params = array(
            'repository' => 'Acteur',
            'orderBy' => array(
                'Acteur.nom' => 'ASC',
                'Acteur.prenom' => 'ASC',
                'Acteur.nom_usage' => 'ASC'
            )
        );
        $acteurs = $repository->findAllElements($page, self::MAX_RESULT, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'acteur_liste',
            'nbr_page' => ceil($acteurs['total'] / self::MAX_RESULT),
            'total' => $acteurs['total'],
            'route_params' => array()
        );
        
        $paths = array(
            'active' => 'Acteurs'
        );
        
        return $this->render('acteur/index.html.twig', array(
            'acteurs' => $acteurs['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }
    
    /**
     * Affichage d'un acteur
     *
     * @Route("/acteur/{slug}/afficher/{page}", name="acteur_afficher")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Acteur $acteur
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficher(Acteur $acteur, int $page = 1)
    {
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('acteur_liste', array(
                    'page' => $page
                )) => 'Acteurs'
            ),
            'active' => 'Affichage' . $this->getIdNom($acteur, 'acteur')
        );
        
        return $this->render('acteur/afficher.html.twig', array(
            'acteur' => $acteur,
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
    * Formulaire d'ajout d'un acteur
    *
    * @Route("/acteur/ajouter/{page}", name="acteur_ajouter")
    * @IsGranted("ROLE_UTILISATEUR")
    *
    * @param Request $request
    * @param int $page
    * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    */
    public function ajouter(Request $request, int $page = 1)
    {
        $acteur = new Acteur();
        
        $form = $this->createForm(ActeurType::class, $acteur);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $acteur = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            
            $slug = $this->createSlug($acteur->getNomComplet(), 'Acteur');
            $acteur->setSlug($slug);
            
            $manager->persist($acteur);
            $manager->flush();
            
            return $this->redirectToRoute('acteur_afficher', array(
                'slug' => $acteur->getSlug(),
                'page' => $page
            ));
        }
        
        $paths = array(
            'urls' => array(
                $this->generateUrl('acteur_liste', array(
                    'page' => $page
                )) => 'Acteurs'
            ),
            'active' => 'Ajouter un acteur'
        );
        
        return $this->render('acteur/ajouter.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'un acteur
     *
     * @Route("/acteur/{slug}/modifier/{page}", name="acteur_modifier")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param Acteur $acteur
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, Acteur $acteur, int $page = 1)
    {
        $form = $this->createForm(ActeurType::class, $acteur);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $acteur = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($acteur);
            $manager->flush();
            
            return $this->redirectToRoute('acteur_afficher', array(
                'slug' => $acteur->getSlug(),
                'page' => $page
            ));
        }
        
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('acteur_liste', array(
                    'page' => $page
                )) => 'Acteurs',
                $this->generateUrl('acteur_afficher', array(
                    'slug' => $acteur->getSlug(),
                    'page' => $page
                )) => $acteur->getNomComplet()
            ),
            'active' => 'Modification' . $this->getIdNom($acteur, 'acteur')
        );
        
        return $this->render('acteur/modifier.html.twig', array(
            'form' => $form->createView(),
            'acteur' => $acteur,
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de suppression d'un acteur
     *
     * @Route("/acteur/supprimer/{slug}", name="acteur_supprimer")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param Acteur $acteur
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Acteur $acteur){
        $manager = $this->getDoctrine()->getManager();
        
        $nationalites = $acteur->getNationalites();
        foreach ($nationalites as $nationalite){
            $acteur->removeNationalite($nationalite);
        }
        
        $acteurFilms = $acteur->getActeurFilms();
        foreach($acteurFilms as $acteurFilm){
            $manager->remove($acteurFilm);
        }
        
        $acteurSaisons = $acteur->getActeurSaisons();
        foreach($acteurSaisons as $acteurSaison){
            $manager->remove($acteurSaison);
        }
        
        $manager->persist($acteur);
        
        $manager->remove($acteur);
        $manager->flush();
        
        return $this->redirectToRoute('acteur_liste');
    }
}
