<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Episode;
use App\Entity\Saison;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EpisodeType;

class EpisodeController extends AppController
{

    /**
     * Liste des épisodes
     *
     * @Route("/episode/liste/{page}", name="episode_liste")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(SessionInterface $session, int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Episode::class);
        if ($session->get('user')['episode_vo']) {
            $order = 'Episode.titre_original';
        } else {
            $order = 'Episode.titre';
        }

        $params = array(
            'repository' => 'Episode',
            'orderBy' => array(
                $order => 'ASC'
            )
        );
        $episodes = $repository->findAllElements($page, self::MAX_RESULT, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'episode_liste',
            'nbr_page' => ceil($episodes['total'] / self::MAX_RESULT),
            'total' => $episodes['total'],
            'route_params' => array()
        );

        $paths = array(
            'active' => 'Épisodes'
        );

        return $this->render('episode/index.html.twig', array(
            'episodes' => $episodes['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }

    /**
     * Affichage d'un épisode
     *
     * @Route("/episode/{slug}/afficher/{page}", name="episode_afficher")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Episode $episode
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficher(Episode $episode, int $page = 1)
    {
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('episode_liste', array(
                    'page' => $page
                )) => 'Épisodes'
            ),
            'active' => 'Affichage' . $this->getIdNom($episode, 'episode')
        );

        return $this->render('episode/afficher.html.twig', array(
            'episode' => $episode,
            'page' => $page,
            'paths' => $paths
        ));
    }

    /**
     * Formulaire d'ajout d'un épisode
     *
     * @Route("/episode/ajouter/{page}", name="episode_ajouter")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request, int $page = 1)
    {
        $episode = new Episode();

        $form = $this->createForm(EpisodeType::class, $episode);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episode = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            $titre_complet = $episode->getTitreComplet($this->get('session')->get('user')['episode_vo']);
            $slug = $this->createSlug($titre_complet, 'Episode');
            $episode->setSlug($slug);

            $manager->persist($episode);
            $manager->flush();

            return $this->redirectToRoute('episode_afficher', array(
                'slug' => $episode->getSlug(),
                'page' => $page
            ));
        }

        $paths = array(
            'urls' => array(
                $this->generateUrl('episode_liste', array(
                    'page' => $page
                )) => 'Episodes'
            ),
            'active' => 'Ajouter un episode'
        );

        return $this->render('episode/ajouter.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'un épisode
     *
     * @Route("/episode/{slug}/modifier/{page}", name="episode_modifier")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param Episode $episode
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, Session $session, Episode $episode, int $page = 1)
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $episode = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($episode);
            $manager->flush();
            
            return $this->redirectToRoute('episode_afficher', array(
                'slug' => $episode->getSlug(),
                'page' => $page
            ));
        }
        
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('episode_liste', array(
                    'page' => $page
                )) => 'Episodes',
                $this->generateUrl('episode_afficher', array(
                    'slug' => $episode->getSlug(),
                    'page' => $page
                )) => $episode->getTitreComplet($session->get('user')['episode_vo'])
            ),
            'active' => 'Modification' . $this->getIdNom($episode, 'episode')
        );
        
        return $this->render('episode/modifier.html.twig', array(
            'form' => $form->createView(),
            'episode' => $episode,
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de suppression d'un episode
     *
     * @Route("/episode/supprimer/{slug}", name="episode_supprimer")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param Episode $episode
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Episode $episode){
        $manager = $this->getDoctrine()->getManager();
        
        $episodePersonnes = $episode->getEpisodePersonnes();
        foreach($episodePersonnes as $episodePersonne){
            $manager->remove($episodePersonne);
        }
        
        $manager->remove($episode);
        $manager->flush();
        
        return $this->redirectToRoute('episode_liste');
    }

    /**
     * Affichage des épisodes d'une saison
     *
     * @Route("/episode/{slug}/afficher_saison", name="episode_saison_afficher")
     * @IsGranted("ROLE_UTILISATEUR")
     * 
     * @param Saison $saison
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherPourSaison(Saison $saison)
    {
        return $this->render('episode/afficher_pour_saison.html.twig', array(
            'saison' => $saison,
            'episodes' => $saison->getEpisodes()
        ));
    }
}
