<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Nationalite;
use App\Form\NationaliteType;

class NationaliteController extends AppController
{
    /**
     * Liste des nationalités
     *
     * @Route("/nationalite/liste/{page}", name="nationalite_liste")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Nationalite::class);
        
        $params = array(
            'repository' => 'Nationalite',
            'orderBy' => array(
                'Nationalite.pays' => 'ASC'
            )
        );
        $nationalites = $repository->findAllElements($page, self::MAX_RESULT, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'nationalite_liste',
            'nbr_page' => ceil($nationalites['total'] / self::MAX_RESULT),
            'total' => $nationalites['total'],
            'route_params' => array()
        );
        
        $paths = array(
            'active' => 'Nationalités'
        );
        
        return $this->render('nationalite/index.html.twig', array(
            'nationalites' => $nationalites['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }
    
    /**
     * Affichage d'une nationalité
     *
     * @Route("/nationalite/{slug}/afficher/{page}", name="nationalite_afficher")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Nationalite $nationalite
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficher(Nationalite $nationalite, int $page = 1)
    {
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('nationalite_liste', array(
                    'page' => $page
                )) => 'Nationalités'
            ),
            'active' => 'Affichage' . $this->getIdNom($nationalite, 'Nationalite')
        );
        
        return $this->render('nationalite/afficher.html.twig', array(
            'nationalite' => $nationalite,
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire d'ajout d'une nationalité
     *
     * @Route("/nationalite/ajouter", name="nationalite_ajouter")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request)
    {
        $nationalite = new Nationalite();
        
        $form = $this->createForm(NationaliteType::class, $nationalite);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager = $this->getDoctrine()->getManager();
            
            if(!is_null($nationalite->getEtat())){
                $fullname = $nationalite->getPays() . " " . $nationalite->getEtat();
            } else {
                $fullname = $nationalite->getPays();
            }
            
            $slug = $this->createSlug($fullname, 'Nationalite');
            $nationalite->setSlug($slug);
            
            $manager->persist($nationalite);
            $manager->flush();
            
            return $this->redirectToRoute('nationalite_liste');
        }
        
        $paths = array(
            'urls' => array(
                $this->generateUrl('nationalite_liste') => 'Nationalités'
            ),
            'active' => 'Ajouter une nationalité'
        );
        
        return $this->render('nationalite/ajouter.html.twig', array(
            'form' => $form->createView(),
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'une naionalité
     *
     * @Route("/nationalite/modifier/{id}/{page}", name="nationalite_modifier")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param Nationalite $nationalite
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, Nationalite $nationalite, int $page = 1)
    {
        $form = $this->createForm(NationaliteType::class, $nationalite);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $nationalite = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($nationalite);
            $manager->flush();
            
            return $this->redirectToRoute('nationalite_afficher', array(
                'slug' => $nationalite->getSlug(),
                'page' => $page
            ));
        }
        
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('nationalite_liste', array(
                    'page' => $page
                )) => 'Nationalités'
            ),
            'active' => 'Modification' . $this->getIdNom($nationalite, 'nationalite')
        );
        
        return $this->render('nationalite/modifier.html.twig', array(
            'form' => $form->createView(),
            'nationalite' => $nationalite,
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de suppression d'une nationalité
     *
     * @Route("/nationalite/supprimer/{id}", name="nationalite_supprimer")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param Nationalite $nationalite
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Nationalite $nationalite){
        $manager = $this->getDoctrine()->getManager();
        
        $acteurs = $nationalite->getActeurs();
        foreach ($acteurs as $acteur){
            $nationalite->removeActeur($acteur);
        }
        
        $manager->persist($nationalite);
        
        $manager->remove($nationalite);
        $manager->flush();
        
        return $this->redirectToRoute('nationalite_liste');
    }
}
