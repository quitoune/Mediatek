<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Episode;
use App\Entity\EpisodePersonne;
use App\Form\EpisodePersonneType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Lieu;
use App\Entity\Personne;
use App\Entity\Saison;
use App\Entity\Format;

class EpisodePersonneController extends AppController
{

    /**
     *
     * @Route("/episode_personne", name="episode_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     */
    public function index()
    {
        return $this->render('episode_personne/index.html.twig', [
            'controller_name' => 'EpisodePersonneController'
        ]);
    }

    /**
     * Affichage des propriétaires d'un épisode
     *
     * @Route("/episode_personne/{slug}/afficher_pour_episode", name="personne_pour_episode")
     *
     * @param SessionInterface $session
     * @param Episode $episode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherProprios(SessionInterface $session, Episode $episode)
    {
        $repo = $this->getDoctrine()->getRepository(EpisodePersonne::class);
        $personnes = array();
        foreach ($session->get('personnes') as $personne) {
            $personnes[] = $personne["id"];
        }

        return $this->render('episode_personne/afficher_proprios_episode.html.twig', array(
            'episode' => $episode,
            'episodePersonnes' => $repo->getEpisodePersonnes($episode->getId(), $personnes)
        ));
    }

    /**
     * Ajout d'un propriétaire à un épisode
     *
     * @Route("/episode_personne/{slug}/ajax_ajouter_personne", name="ajax_episode_personne_ajouter_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Episode $episode
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouterDepuisEpisode(SessionInterface $session, Request $request, Episode $episode)
    {
        $episodePersonne = new EpisodePersonne();
        $episodePersonne->setEpisode($episode);

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

        $form = $this->createForm(EpisodePersonneType::class, $episodePersonne, array(
            'avec_episode' => false,
            'select_personne' => $select_personne,
            'select_lieu' => $select_lieu
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($episodePersonne);
            $manager->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('episode_personne/ajax_ajouter_personne.html.twig', array(
            'form' => $form->createView(),
            'episode' => $episode
        ));
    }

    /**
     * Edition lien personne - épisode depuis épisode
     *
     * @Route("/episode_personne/{id}/ajax_editer_personne", name="ajax_episode_personne_editer_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param EpisodePersonne $episodePersonne
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editerDepuisEpisode(SessionInterface $session, Request $request, EpisodePersonne $episodePersonne)
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

        $form = $this->createForm(EpisodePersonneType::class, $episodePersonne, array(
            'avec_episode' => false,
            'select_personne' => $select_personne,
            'select_lieu' => $select_lieu
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($episodePersonne);
            $manager->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('episode_personne/ajax_editer_personne.html.twig', array(
            'form' => $form->createView(),
            'episodePersonne' => $episodePersonne
        ));
    }

    /**
     * Suppression lien personne - épisode depuis épisode
     *
     * @Route("/episode_personne/{id}/ajax_supprimer_personne", name="ajax_episode_personne_supprimer_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param EpisodePersonne $episodePersonne
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function supprimerDepuisEpisode(EpisodePersonne $episodePersonne)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($episodePersonne);
        $manager->flush();

        return $this->json(array(
            'statut' => true
        ));
    }

    /**
     * Ajout d'un propriétaire à tous les épisodes d'une saison
     *
     * @Route("/episode_personne/{slug}/ajax_ajouter_depuis_saison", name="ajax_ajouter_depuis_saison")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param Saison $saison
     */
    public function ajouterDepuisSaison(SessionInterface $session, Request $request, Saison $saison)
    {
        $repo_lieu = $this->getDoctrine()->getRepository(Lieu::class);
        $repo_format = $this->getDoctrine()->getRepository(Format::class);
        $repo_pers = $this->getDoctrine()->getRepository(Personne::class);

        if (count($request->request->all())) {
            $all = $request->request->all()['episode_personne'];

            $format = $repo_format->findOneBy(array(
                'id' => $all['format']
            ));

            $lieu = $repo_lieu->findOneBy(array(
                'id' => $all['lieu']
            ));

            $personne = $repo_pers->findOneBy(array(
                'id' => $all['personne']
            ));

            $manager = $this->getDoctrine()->getManager();
            foreach ($saison->getEpisodes() as $episode) {
                $episodePersonne = new EpisodePersonne();

                $episodePersonne->setPersonne($personne);
                $episodePersonne->setEpisode($episode);
                $episodePersonne->setFormat($format);
                $episodePersonne->setLieu($lieu);
                if ($all['date_achat']['day'] && $all['date_achat']['month'] && $all['date_achat']['year']) {
                    $date = $all['date_achat']['year'] . "-" . $all['date_achat']['month'] . "-" . $all['date_achat']['day'];
                    $episodePersonne->setDateAchat(new \DateTime($date));
                }

                $manager->persist($episodePersonne);
            }

            $manager->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        $personnes = array();
        $lieux = array();
        
        $repo_lieu = $this->getDoctrine()->getRepository(Lieu::class);
        $repo_pers = $this->getDoctrine()->getRepository(Personne::class);
        
        foreach ($session->get('personnes') as $personne) {
            $personnes[] = $repo_pers->findOneBy(array(
                'id' => $personne["id"]
            ));
        }
        
        foreach ($session->get('lieux') as $lieu) {
            $lieux[] = $repo_lieu->findOneBy(array(
                'id' => $lieu["id"]
            ));
        }

        $formats = $repo_format->getFormatsForMovies();

        return $this->render('episode_personne/ajax_ajouter_depuis_saison.html.twig', array(
            'personnes' => $personnes,
            'formats' => $formats,
            'saison' => $saison,
            'lieux' => $lieux,
            'date_debut' => date("Y") - 15,
            'date_fin' => date("Y")
        ));
    }
}
