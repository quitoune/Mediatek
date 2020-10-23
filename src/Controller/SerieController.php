<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Serie;
use App\Form\SerieType;
use App\Entity\Saison;

class SerieController extends AppController
{

    /**
     * Liste des séries
     *
     * @Route("/serie/liste/{page}", name="serie_liste")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Serie::class);

        $params = array(
            'repository' => 'Serie',
            'orderBy' => array(
                'Serie.titre_original' => 'ASC'
            )
        );
        $series = $repository->findAllElements($page, self::MAX_RESULT, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'serie_liste',
            'nbr_page' => ceil($series['total'] / self::MAX_RESULT),
            'total' => $series['total'],
            'route_params' => array()
        );

        $paths = array(
            'active' => 'Séries'
        );

        return $this->render('serie/index.html.twig', array(
            'series' => $series['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }

    /**
     * Affichage d'une série
     *
     * @Route("/serie/{slug}/afficher/{page}", name="serie_afficher")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Serie $serie
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficher(Serie $serie, int $page = 1)
    {
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('serie_liste', array(
                    'page' => $page
                )) => 'Séries'
            ),
            'active' => 'Affichage' . $this->getIdNom($serie, 'serie')
        );

        return $this->render('serie/afficher.html.twig', array(
            'serie' => $serie,
            'page' => $page,
            'paths' => $paths
        ));
    }

    /**
     * Ajouter d'une série
     *
     * @Route("/serie/ajouter/{page}", name="serie_ajouter")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request, int $page = 1)
    {
        $serie = new Serie();

        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serie = $form->getData();

            $manager = $this->getDoctrine()->getManager();

            if (isset($request->request->all()['serie']['saisons'])) {
                foreach ($request->request->all()['serie']['saisons'] as $serie_saison) {
                    $saison = new Saison();
                    if (isset($serie_saison['nom']) && $serie_saison['nom']) {
                        $saison->setNom($serie_saison['nom']);
                    }
                    $saison->setNombreEpisode($serie_saison['nombre_episode']);
                    $saison->setNumeroSaison($serie_saison['numero_saison']);

                    $nom = $serie->getTitreOriginal() . '-Saison ' . $serie_saison['numero_saison'];
                    $saison->setSlug($this->createSlug($nom, 'Saison'));
                    $saison->setSerie($serie);
                    $manager->persist($saison);
                }
            }

            $slug = $this->createSlug($serie->getTitreOriginal(), 'Serie', $serie->getAnnee());
            $serie->setSlug($slug);

            $manager->persist($serie);
            $manager->flush();

            return $this->redirectToRoute('serie_afficher', array(
                'slug' => $serie->getSlug(),
                'page' => $page
            ));
        }

        $paths = array(
            'urls' => array(
                $this->generateUrl('serie_liste', array(
                    'page' => $page
                )) => 'Séries'
            ),
            'active' => 'Ajouter une série'
        );

        return $this->render('serie/ajouter.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,
            'paths' => $paths
        ));
    }

    /**
     * Ajouter d'une série
     *
     * @Route("/serie/{slug}/modifier/{page}", name="serie_modifier")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, Serie $serie, int $page = 1)
    {
        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serie = $form->getData();

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($serie);
            $manager->flush();

            return $this->redirectToRoute('serie_afficher', array(
                'slug' => $serie->getSlug(),
                'page' => $page
            ));
        }

        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('serie_liste', array(
                    'page' => $page
                )) => 'Séries',
                $this->generateUrl('serie_afficher', array(
                    'slug' => $serie->getSlug(),
                    'page' => $page
                )) => $serie->getTitreComplet($this->get('session')->get('user')['episode_vo'])
            ),
            'active' => 'Modification' . $this->getIdNom($serie, 'serie')
        );

        return $this->render('serie/modifier.html.twig', array(
            'serie' => $serie,
            'form' => $form->createView(),
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de suppression d'une série
     *
     * @Route("/serie/supprimer/{slug}", name="serie_supprimer")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param Saison $saison
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Serie $serie){
        $manager = $this->getDoctrine()->getManager();
        
        $saisons = $serie->getSaisons();
        foreach ($saisons as $saison){
            $acteurSaisons = $saison->getActeurSaisons();
            foreach($acteurSaisons as $acteurSaison){
                $manager->remove($acteurSaison);
            }
            
            $episodes = $saison->getEpisodes();
            foreach($episodes as $episode){
                foreach ($episode->getEpisodePersonnes() as $episodePersonne){
                    $manager->remove($episodePersonne);
                }
                $manager->remove($episode);
            }
            $manager->remove($saison);
        }
        
        $manager->remove($serie);
        $manager->flush();
        
        return $this->redirectToRoute('serie_liste');
    }
}
