<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Film;
use App\Entity\FilmPersonne;
use App\Form\FilmPersonneType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Lieu;
use App\Entity\Personne;

class FilmPersonneController extends AppController
{

    /**
     *
     * @Route("/film_personne", name="film_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     */
    public function index()
    {
        return $this->render('film_personne/index.html.twig', [
            'controller_name' => 'FilmPersonneController'
        ]);
    }

    /**
     * Affichage des propriétaires d'un film
     *
     * @Route("/film_personne/{slug}/afficher_pour_film", name="personne_pour_film")
     *
     * @param SessionInterface $session
     * @param Film $film
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherProprios(SessionInterface $session, Film $film)
    {
        $repo = $this->getDoctrine()->getRepository(FilmPersonne::class);
        $personnes = array();
        foreach ($session->get('personnes') as $personne) {
            $personnes[] = $personne["id"];
        }

        return $this->render('film_personne/afficher_proprios_film.html.twig', array(
            'film' => $film,
            'filmPersonnes' => $repo->getFilmPersonnes($film->getId(), $personnes)
        ));
    }

    /**
     * Ajout d'un propriétaire à un film
     *
     * @Route("/film_personne/{slug}/ajax_ajouter_personne", name="ajax_film_personne_ajouter_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Film $film
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouterDepuisFilm(SessionInterface $session, Request $request, Film $film)
    {
        $filmPersonne = new FilmPersonne();
        $filmPersonne->setFilm($film);

        $select_personne = array();
        $select_lieu = array();

        $repo_lieu = $this->getDoctrine()->getRepository(Lieu::class);
        $repo_pers = $this->getDoctrine()->getRepository(Personne::class);

        foreach ($session->get('personnes') as $personne) {
            $select_personne[] = $repo_pers->findOneBy(array(
                'id' => $personne["id"]
            ));
        }

        foreach ($session->get('lieux') as $lieu) {
            $select_lieu[] = $repo_lieu->findOneBy(array(
                'id' => $lieu["id"]
            ));
        }

        $form = $this->createForm(FilmPersonneType::class, $filmPersonne, array(
            'avec_film' => false,
            'select_personne' => $select_personne,
            'select_lieu' => $select_lieu
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($filmPersonne);
            $manager->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('film_personne/ajax_ajouter_personne.html.twig', array(
            'form' => $form->createView(),
            'film' => $film
        ));
    }

    /**
     * Edition lien personne - film depuis film
     *
     * @Route("/film_personne/{id}/ajax_editer_personne", name="ajax_film_personne_editer_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param FilmPersonne $filmPersonne
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editerDepuisFilm(SessionInterface $session, Request $request, FilmPersonne $filmPersonne)
    {
        $select_personne = array();
        $select_lieu = array();

        $repo_lieu = $this->getDoctrine()->getRepository(Lieu::class);
        $repo_pers = $this->getDoctrine()->getRepository(Personne::class);

        foreach ($session->get('personnes') as $personne) {
            $select_personne[] = $repo_pers->findOneBy(array(
                'id' => $personne["id"]
            ));
        }

        foreach ($session->get('lieux') as $lieu) {
            $select_lieu[] = $repo_lieu->findOneBy(array(
                'id' => $lieu["id"]
            ));
        }

        $form = $this->createForm(FilmPersonneType::class, $filmPersonne, array(
            'avec_film' => false,
            'select_personne' => $select_personne,
            'select_lieu' => $select_lieu
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($filmPersonne);
            $manager->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('film_personne/ajax_editer_personne.html.twig', array(
            'form' => $form->createView(),
            'filmPersonne' => $filmPersonne
        ));
    }

    /**
     * Suppression lien personne - film depuis film
     *
     * @Route("/film_personne/{id}/ajax_supprimer_personne", name="ajax_film_personne_supprimer_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param FilmPersonne $filmPersonne
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function supprimerDepuisFilm(FilmPersonne $filmPersonne)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($filmPersonne);
        $manager->flush();

        return $this->json(array(
            'statut' => true
        ));
    }
}
