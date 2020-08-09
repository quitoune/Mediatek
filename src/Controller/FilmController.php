<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Film;
use App\Entity\Acteur;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FilmType;

class FilmController extends AppController
{
    /**
     * Liste des films
     *
     * @Route("/film/liste/{page}", name="film_liste")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(SessionInterface $session, int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Film::class);
        if($session->get('user')['film_vo']){
            $order = 'Film.titre_original';
        } else {
            $order = 'Film.titre';
        }

        $params = array(
            'repository' => 'Film',
            'orderBy' => array(
                $order => 'ASC'
            )
        );
        $films = $repository->findAllElements($page, self::MAX_RESULT, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'film_liste',
            'nbr_page' => ceil($films['total'] / self::MAX_RESULT),
            'total' => $films['total'],
            'route_params' => array()
        );

        $paths = array(
            'active' => 'Films'
        );

        return $this->render('film/index.html.twig', array(
            'films' => $films['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }

    /**
     *
     * @Route("/film/{slug}/afficher/{page}", name="film_afficher")
     *
     * @param Film $film
     * @param int $page
     */
    public function afficher(Film $film, int $page = 1)
    {
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('film_liste', array(
                    'page' => $page
                )) => 'Films'
            ),
            'active' => 'Affichage' . $this->getIdNom($film, 'film')
        );

        return $this->render('film/afficher.html.twig', array(
            'film' => $film,
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire d'ajout d'un film
     *
     * @Route("/film/ajouter", name="film_ajouter")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request, SessionInterface $session)
    {
        $film = new Film();
        
        $form = $this->createForm(FilmType::class, $film);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager = $this->getDoctrine()->getManager();
            
            $slug = $this->createSlug($film->getTitreOriginal(), 'Film');
            $film->setSlug($slug);
            
            $manager->persist($film);
            $manager->flush();
            
            return $this->redirectToRoute('film_afficher', array(
                'slug' => $film->getSlug()
            ));
        }
        
        $paths = array(
            'urls' => array(
                $this->generateUrl('film_liste') => 'Films'
            ),
            'active' => 'Ajouter un film'
        );
        
        return $this->render('film/ajouter.html.twig', array(
            'form' => $form->createView(),
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'un film
     *
     * @Route("/film/{slug}/modifier/{page}", name="film_modifier")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, SessionInterface $session, Film $film, int $page = 1)
    {
        $form = $this->createForm(FilmType::class, $film);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager = $this->getDoctrine()->getManager();
            
            $manager->persist($film);
            $manager->flush();
            
            return $this->redirectToRoute('film_afficher', array(
                'slug' => $film->getSlug()
            ));
        }
        
        $paths = array(
            'urls' => array(
                $this->generateUrl('film_liste', array(
                    'page' => $page
                )) => 'Films',
                $this->generateUrl('film_afficher', array(
                    'slug' => $film->getSlug(),
                    'page' => $page
                )) => $film->getTitreComplet($session->get('user')['film_vo'])
            ),
            'active' => 'Modification' . $this->getIdNom($film, 'film')
        );
        
        return $this->render('film/modifier.html.twig', array(
            'form' => $form->createView(),
            'film' => $film,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de suppression d'un film
     *
     * @Route("/film/supprimer/{slug}", name="film_supprimer")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Film $film
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Film $film){
        $manager = $this->getDoctrine()->getManager();
        
        $filmPersonnes = $film->getFilmPersonnes();
        foreach($filmPersonnes as $filmPersonne){
            $film->removeFilmPersonne($filmPersonne);
            $manager->remove($filmPersonne);
        }
        
        $acteurFilms = $film->getActeurFilms();
        foreach($acteurFilms as $acteurFilm){
            $manager->remove($acteurFilm);
        }
        
        $manager->remove($film);
        $manager->flush();
        
        return $this->redirectToRoute('film_liste');
    }

    /**
     *
     * @Route("/film_acteur/{slug}/afficher/{page}", name="film_afficher_pour_acteur")
     *
     * @param Acteur $acteur
     * @param int $page
     */
    public function afficherPourActeur(Acteur $acteur, int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Film::class);

        $params = array(
            'repository' => 'Film',
            'orderBy' => array(
                'Film.titre_original' => 'ASC'
            ),
            'condition' => array(
                'acteur.id = ' . $acteur->getId()
            ),
            'jointure' => array(
                'Film' => 'acteurFilms',
                'acteurFilms' => 'acteur'
            )
        );
        $films = $repository->findAllElements($page, self::MAX_AJAX_RESULT, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'film_afficher_pour_acteur',
            'nbr_page' => ceil($films['total'] / self::MAX_AJAX_RESULT),
            'total' => $films['total'],
            'route_params' => array(
                'slug' => $acteur->getSlug()
            )
        );

        return $this->render('film/afficher_pour_acteur.html.twig', array(
            'bloc' => 'collapseFilms',
            'films' => $films['paginator'],
            'acteur' => $acteur,
            'pagination' => $pagination
        ));
    }
}
