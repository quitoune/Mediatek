<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Saison;
use App\Entity\Episode;
use App\Entity\EpisodePersonne;
use App\Form\EpisodePersonneType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Format;
use App\Entity\Lieu;
use Symfony\Component\Validator\Constraints\Date;
use App\Entity\Personne;

class EpisodePersonneController extends AppController
{

    /**
     *
     * @Route("/episode_personne", name="episode_personne")
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
     * @param Episode $episode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherProprios(Episode $episode)
    {
        return $this->render('episode_personne/afficher_proprios_episode.html.twig', array(
            'episode' => $episode,
            'episodePersonnes' => $episode->getEpisodePersonnes()
        ));
    }

    /**
     * Ajout d'un propriétaire à un épisode
     *
     * @Route("/episode_personne/{slug}/ajax_ajouter_personne", name="ajax_episode_personne_ajouter_personne")
     *
     * @param Request $request
     * @param Episode $episode
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouterDepuisEpisode(Request $request, Episode $episode)
    {
        $episodePersonne = new EpisodePersonne();
        $episodePersonne->setEpisode($episode);

        $form = $this->createForm(EpisodePersonneType::class, $episodePersonne, array(
            'avec_episode' => false
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episodePersonne = $form->getData();

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
     *
     * @param Request $request
     * @param Episode $episode
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editerDepuisEpisode(Request $request, EpisodePersonne $episodePersonne)
    {
        $form = $this->createForm(EpisodePersonneType::class, $episodePersonne, array(
            'avec_episode' => false
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episodePersonne = $form->getData();

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
     *
     * @param Request $request
     * @param Saison $saison
     */
    public function ajouterDepuisSaison(Request $request, Saison $saison)
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
                if($all['date_achat']['day'] && $all['date_achat']['month'] && $all['date_achat']['year']){
                    $date = $all['date_achat']['year'] . "-" . $all['date_achat']['month'] . "-" . $all['date_achat']['day'];
                    $episodePersonne->setDateAchat(new Date($date));
                }
                
                $manager->persist($episodePersonne);
            }

            $manager->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        $personnes = $repo_pers->createQueryBuilder('personne')
            ->orderBy('personne.nom')
            ->addOrderBy('personne.prenom')
            ->getQuery()
            ->getResult();

        $formats = $repo_format->createQueryBuilder('format')
            ->andWhere('format.objet IN (0,2)')
            ->orderBy('format.nom')
            ->getQuery()
            ->getResult();

        $lieux = $repo_lieu->createQueryBuilder('lieu')
            ->orderBy('lieu.nom')
            ->getQuery()
            ->getResult();

        return $this->render('episode_personne/ajax_ajouter_depuis_saison.html.twig', array(
            'personnes' => $personnes,
            'formats' => $formats,
            'saison' => $saison,
            'lieux' => $lieux
        ));
    }
}
