<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Film;
use App\Entity\Acteur;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FilmType;
use App\Entity\Lieu;
use App\Entity\Format;
use App\Entity\Personne;
use App\Entity\FilmPersonne;

class FilmController extends AppController
{
    /**
     * Liste des films
     *
     * @Route("/film/liste/{page}", name="film_liste")
     * @IsGranted("ROLE_UTILISATEUR")
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
     * @IsGranted("ROLE_UTILISATEUR")
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
     * @IsGranted("ROLE_UTILISATEUR")
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
            
            $filmPersonnes = $request->request->all()['film']['filmPersonnes'];
            foreach($filmPersonnes as $filmPersonne){
                $repo_lieu = $this->getDoctrine()->getRepository(Lieu::class);
                $repo_format = $this->getDoctrine()->getRepository(Format::class);
                $repo_pers = $this->getDoctrine()->getRepository(Personne::class);
                
                $film_personne = new FilmPersonne();
                
                $format = $repo_format->findOneBy(array(
                    'id' => $filmPersonne['format']
                ));
                
                $lieu = $repo_lieu->findOneBy(array(
                    'id' => $filmPersonne['lieu']
                ));
                
                $personne = $repo_pers->findOneBy(array(
                    'id' => $filmPersonne['personne']
                ));
                
                $film_personne->setLieu($lieu);
                $film_personne->setFormat($format);
                $film_personne->setPersonne($personne);
                if($filmPersonne['date_achat']['day'] && $filmPersonne['date_achat']['month'] && $filmPersonne['date_achat']['year']){
                    $date  = $filmPersonne['date_achat']['year'] . "-";
                    $date .= ($filmPersonne['date_achat']['month'] < 10 ? "0" : "") . $filmPersonne['date_achat']['month'] . "-";
                    $date .= ($filmPersonne['date_achat']['day'] < 10 ? "0" : "") . $filmPersonne['date_achat']['day'];
                    $film_personne->setDateAchat(new \DateTime($date));
                }
                $film_personne->setFilm($film);
                $manager->persist($film_personne);
            }
            
            $manager->persist($film);
            $manager->flush();
            
            return $this->redirectToRoute('film_afficher', array(
                'slug' => $film->getSlug()
            ));
        }
        
        $personnes = array();
        foreach ($session->get('personnes') as $pers){
            $personnes[$pers['id']] = $pers['username'];
        }
        
        $forms = $this->getDoctrine()->getRepository(Format::class)->getFormatsForMovies();
        $formats = array();
        foreach($forms as $format){
            $formats[$format->getId()] = $format->getNom();
        }
        
        $lieux = array();
        foreach ($session->get('lieux') as $lieu){
            $lieux[$lieu['id']] = $lieu['nom'];
        }
        
        $paths = array(
            'urls' => array(
                $this->generateUrl('film_liste') => 'Films'
            ),
            'active' => 'Ajouter un film'
        );
        
        return $this->render('film/ajouter.html.twig', array(
            'form' => $form->createView(),
            'personnes' => json_encode($personnes, JSON_UNESCAPED_UNICODE),
            'formats' => json_encode($formats, JSON_UNESCAPED_UNICODE),
            'lieux' => json_encode($lieux, JSON_UNESCAPED_UNICODE),
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'un film
     *
     * @Route("/film/{slug}/modifier/{page}", name="film_modifier")
     * @IsGranted("ROLE_UTILISATEUR")
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
     * @IsGranted("ROLE_SUPER_ADMIN")
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
     * @IsGranted("ROLE_UTILISATEUR")
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
