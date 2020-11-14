<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ActeurFilm;
use App\Entity\Film;
use App\Entity\Acteur;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

class ActeurFilmController extends AppController
{

    /**
     * Rôle d'un acteur dans un film
     *
     * @var array
     */
    const ROLE = array(
        0 => 'Caméo',
        1 => 'Secondaire',
        2 => 'Principal'
    );

    /**
     *
     * @Route("/acteur_film", name="acteur_film")
     */
    public function index()
    {
        return $this->render('acteur_film/index.html.twig', [
            'controller_name' => 'ActeurFilmController'
        ]);
    }

    /**
     * Affichage des acteurs principaux | récurrents | invités d'un film
     *
     * @Route("/acteur_film/{slug}/afficher_pour_film/{principal}", name="acteur_pour_film")
     *
     * @param Film $film
     * @param int $principal
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherActeurPourFilm(Film $film, int $principal)
    {
        $repo = $this->getDoctrine()->getRepository(ActeurFilm::class);
        switch ($principal) {
            case 2:
                $principaux = $repo->getActeurByRole($film->getId(), $principal);
                return $this->render('acteur_film/afficher_principaux_film.html.twig', array(
                    'film' => $film,
                    'principaux' => $principaux
                ));
                break;
            case 1:
                $recurrents = $repo->getActeurByRole($film->getId(), $principal);
                return $this->render('acteur_film/afficher_recurrents_film.html.twig', array(
                    'film' => $film,
                    'recurrents' => $recurrents
                ));
                break;
            case 0:
                $invites = $repo->getActeurByRole($film->getId(), $principal);
                return $this->render('acteur_film/afficher_invites_film.html.twig', array(
                    'film' => $film,
                    'invites' => $invites
                ));
                break;
            default:
                break;
        }
    }

    /**
     * Affichage des films d'un acteur
     *
     * @Route("/acteur_film/{slug}/afficher_pour_acteur/{page}", name="film_pour_acteur")
     *
     * @param Acteur $acteur
     * @param int $principal
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherFilmPourActeur(Acteur $acteur, int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(ActeurFilm::class);

        $params = array(
            'repository' => 'ActeurFilm',
            'orderBy' => array(
                'film.annee' => 'DESC'
            ),
            'condition' => array(
                'ActeurFilm.acteur = ' . $acteur->getId()
            ),
            'jointure' => array(
                'ActeurFilm' => 'film'
            )
        );
        $acteurFilms = $repository->findAllElements($page, self::MAX_AJAX_RESULT, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'film_pour_acteur',
            'nbr_page' => ceil($acteurFilms['total'] / self::MAX_AJAX_RESULT),
            'total' => $acteurFilms['total'],
            'route_params' => array(
                'slug' => $acteur->getSlug()
            )
        );

        return $this->render('acteur_film/afficher_pour_acteur.html.twig', array(
            'bloc' => '#films',
            'acteurFilms' => $acteurFilms['paginator'],
            'acteur' => $acteur,
            'pagination' => $pagination
        ));
    }

    /**
     *
     * @Route("/acteur_film/{slug}/ajax_editer_films_acteur", name="ajax_editer_films_acteur")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Acteur $acteur
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editerFilmPourActeur(Request $request, Acteur $acteur, SessionInterface $session)
    {
        $repository = $this->getDoctrine()->getRepository(ActeurFilm::class);
        $repo = $this->getDoctrine()->getRepository(Film::class);
        $repo_films = $repo->findBy(array(), array(
            'titre_original' => 'ASC'
        ));

        if (count($request->request->all())) {
//             $this->pre($request->request->all()['acteur']['acteurFilms']);
//             die();
            $acteurFilms = $request->request->all()['acteur']['acteurFilms'];

            $manager = $this->getDoctrine()->getManager();

            foreach ($acteurFilms as $acteurFilm) {
                $acteur_film = new ActeurFilm();
                if ($acteurFilm['delete']) {
                    $acteur_film = $repository->findOneBy(array(
                        'id' => $acteurFilm['id']
                    ));
                    $manager->remove($acteur_film);
                } else if (isset($acteurFilm['id'])) {
                    $acteur_film = $repository->findOneBy(array(
                        'id' => $acteurFilm['id']
                    ));
                    if ($acteurFilm['role']) {
                        $acteur_film->setRole($acteurFilm['role']);
                    } else {
                        $acteur_film->setRole(NULL);
                    }
                    $acteur_film->setPrincipal($acteurFilm['principal']);
                    $acteur_film->setFilm($repo->findOneBy(array(
                        'id' => $acteurFilm['film']
                    )));
                    $acteur_film->setActeur($acteur);

                    $manager->persist($acteur_film);
                } else {
                    if ($acteurFilm['role']) {
                        $acteur_film->setRole($acteurFilm['role']);
                    } else {
                        $acteur_film->setRole(NULL);
                    }
                    $acteur_film->setPrincipal($acteurFilm['principal']);
                    $acteur_film->setFilm($repo->findOneBy(array(
                        'id' => $acteurFilm['film']
                    )));
                    $acteur_film->setActeur($acteur);

                    $manager->persist($acteur_film);
                }
            }
            $manager->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        $opt_film = array(
            "class" => "form-control",
            "id" => "acteur_acteurFilms_INDEX_film",
            "default" => 1
        );

        $films = array();

        $index = 0;
        foreach ($repo_films as $film) {
            if ($index == 0) {
                $opt_film["default"] = $film->getId();
            }
            $films[$film->getId()] = $film->getTitreComplet($session->get('user')['film_vo']);
            $index ++;
        }

        return $this->render('acteur_film/ajax_editer_films_acteur.html.twig', array(
            'opt_film' => $opt_film,
            'acteur' => $acteur,
            'films' => $films
        ));
    }
    
    /**
     * @Route("/acteur_film/{slug}/ajax_editer_acteurs_film", name="ajax_editer_acteurs_film")
     * @IsGranted("ROLE_UTILISATEUR")
     * 
     * @param Request $request
     * @param Film $film
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editerActeurPourFilm(Request $request, Film $film){
        $repository = $this->getDoctrine()->getRepository(ActeurFilm::class);
        $repo = $this->getDoctrine()->getRepository(Acteur::class);
        $repo_acteurs = $repo->findBy(array(), array(
            'nom' => 'ASC',
            'prenom' => 'ASC',
            'nom_usage' => 'ASC'
        ));
        
        if (count($request->request->all())) {
            //             $this->pre($request->request->all()['film']['acteurFilms']);
            //             die();
            $acteurFilms = $request->request->all()['film']['acteurFilms'];
            
            $manager = $this->getDoctrine()->getManager();
            
            foreach ($acteurFilms as $acteurFilm) {
                $acteur_film = new ActeurFilm();
                if ($acteurFilm['delete']) {
                    $acteur_film = $repository->findOneBy(array(
                        'id' => $acteurFilm['id']
                    ));
                    $manager->remove($acteur_film);
                } else if (isset($acteurFilm['id'])) {
                    $acteur_film = $repository->findOneBy(array(
                        'id' => $acteurFilm['id']
                    ));
                    if ($acteurFilm['role']) {
                        $acteur_film->setRole($acteurFilm['role']);
                    } else {
                        $acteur_film->setRole(NULL);
                    }
                    $acteur_film->setPrincipal($acteurFilm['principal']);
                    $acteur_film->setActeur($repo->findOneBy(array(
                        'id' => $acteurFilm['acteur']
                    )));
                    $acteur_film->setFilm($film);
                    
                    $manager->persist($acteur_film);
                } else {
                    if ($acteurFilm['role']) {
                        $acteur_film->setRole($acteurFilm['role']);
                    } else {
                        $acteur_film->setRole(NULL);
                    }
                    $acteur_film->setPrincipal($acteurFilm['principal']);
                    $acteur_film->setActeur($repo->findOneBy(array(
                        'id' => $acteurFilm['acteur']
                    )));
                    $acteur_film->setFilm($film);
                    
                    $manager->persist($acteur_film);
                }
            }
            $manager->flush();
            
            return $this->json(array(
                'statut' => true
            ));
        }
        
        $opt_acteur = array(
            "class" => "form-control",
            "id" => "film_acteurFilms_INDEX_acteur",
            "default" => 1
        );
        
        $acteurs = array();
        
        $index = 0;
        foreach ($repo_acteurs as $acteur) {
            if ($index == 0) {
                $opt_acteur["default"] = $acteur->getId();
            }
            $acteurs[$acteur->getId()] = $acteur->getNomComplet();
            $index ++;
        }
        
        return $this->render('acteur_film/ajax_editer_acteurs_film.html.twig', array(
            'opt_acteur' => $opt_acteur,
            'film' => $film,
            'acteurs' => $acteurs
        ));
    }
}
