<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Saison;
use App\Entity\ActeurSaison;
use App\Entity\Serie;
use App\Form\SaisonType;

class SaisonController extends AppController
{

    /**
     * Liste des saisons
     *
     * @Route("/saison/liste/{page}", name="saison_liste")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Saison::class);

        $params = array(
            'repository' => 'Saison',
            'orderBy' => array(
                'serie.titre_original' => 'ASC',
                'Saison.numero_saison' => 'ASC'
            ),
            'jointure' => array(
                'Saison' => 'serie'
            )
        );
        $saisons = $repository->findAllElements($page, self::MAX_RESULT, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'saison_liste',
            'nbr_page' => ceil($saisons['total'] / self::MAX_RESULT),
            'total' => $saisons['total'],
            'route_params' => array()
        );

        $paths = array(
            'active' => 'Saisons'
        );

        return $this->render('saison/index.html.twig', array(
            'saisons' => $saisons['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }

    /**
     * Affichage d'une saison
     *
     * @Route("/saison/{slug}/afficher/{page}", name="saison_afficher")
     *
     * @param Saison $saison
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficher(Saison $saison, int $page = 1)
    {
        $repo = $this->getDoctrine()->getRepository(ActeurSaison::class);
        $principaux = $repo->getActeurByRole($saison->getId(), 2);
        $recurrents = $repo->getActeurByRole($saison->getId(), 1);
        $invites = $repo->getActeurByRole($saison->getId(), 0);

        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('saison_liste', array(
                    'page' => $page
                )) => 'Saisons'
            ),
            'active' => 'Affichage' . $this->getIdNom($saison, 'saison')
        );

        return $this->render('saison/afficher.html.twig', array(
            'principaux' => $principaux,
            'recurrents' => $recurrents,
            'invites' => $invites,
            'saison' => $saison,
            'page' => $page,
            'paths' => $paths
        ));
    }

    /**
     * Formulaire d'ajout d'une saison
     *
     * @Route("/saison/ajouter/{page}", name="saison_ajouter")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request, int $page = 1)
    {
        $saison = new Saison();
        $form = $this->createForm(SaisonType::class, $saison, array(
            'vo' => $this->get('session')->get('user')['episode_vo']
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $saison = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            
            $slug = $this->createSlug($saison->getNomComplet(1), 'Saison');
            $saison->setSlug($slug);

            $manager->persist($saison);
            $manager->flush();

            return $this->redirectToRoute('saison_afficher', array(
                'slug' => $saison->getSlug(),
                'page' => $page
            ));
        }

        $paths = array(
            'urls' => array(
                $this->generateUrl('saison_liste', array(
                    'page' => $page
                )) => 'Saisons'
            ),
            'active' => 'Ajouter une saison'
        );

        return $this->render('saison/ajouter.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'une saison
     *
     * @Route("/saison/{slug}/modifier/{page}", name="saison_modifier")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param Request $request
     * @param Saison $saison
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, Saison $saison, int $page = 1)
    {
        $form = $this->createForm(SaisonType::class, $saison, array(
            'vo' => $this->get('session')->get('user')['episode_vo']
        ));
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $saison = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            
            $manager->persist($saison);
            $manager->flush();
            
            return $this->redirectToRoute('saison_afficher', array(
                'slug' => $saison->getSlug(),
                'page' => $page
            ));
        }
        
        $paths = array(
            'urls' => array(
                $this->generateUrl('saison_liste', array(
                    'page' => $page
                )) => 'Saisons',
                $this->generateUrl('saison_afficher', array(
                    'slug' => $saison->getSlug(),
                    'page' => $page
                )) => $saison->getNomComplet($this->get('session')->get('user')['episode_vo'])
            ),
            'active' => 'Modification' . $this->getIdNom($saison, 'Saison')
        );
        
        return $this->render('saison/modifier.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de suppression d'une saison
     *
     * @Route("/saison/supprimer/{slug}", name="saison_supprimer")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Saison $saison
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Saison $saison){
        $manager = $this->getDoctrine()->getManager();
        
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
        $manager->flush();
        
        return $this->redirectToRoute('saison_liste');
    }

    /**
     * Affichage des épisodes d'une saison
     *
     * @Route("/saison/{slug}/saison_serie_afficher", name="saison_serie_afficher")
     * 
     * @param Serie $serie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherPourSerie(Serie $serie)
    {
        return $this->render('saison/afficher_pour_serie.html.twig', array(
            'serie' => $serie,
            'saisons' => $serie->getSaisons()
        ));
    }
    
    /**
     * Affichage des épisodes d'une saison
     *
     * @Route("/saison/{slug}/ajax_ajouter_saisons", name="ajax_ajouter_saisons")
     * 
     * @param Request $request
     * @param Serie $serie
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouterDepuisSerie(Request $request, Serie $serie){
        if(count($request->request->all())){
            $manager = $this->getDoctrine()->getManager();
            foreach ($request->request->all()['serie']['saisons'] as $serie_saison){
                $saison = new Saison();
                if(isset($serie_saison['nom']) && $serie_saison['nom']){
                    $saison->setNom($serie_saison['nom']);
                }
                $saison->setNombreEpisode($serie_saison['nombre_episode']);
                $saison->setNumeroSaison($serie_saison['numero_saison']);
                
                $nom = $serie->getTitreOriginal() . '-Saison ' . $serie_saison['numero_saison'];
                $saison->setSlug($this->createSlug($nom, 'Saison'));
                $saison->setSerie($serie);
                $manager->persist($saison);
            }
            
            $manager->flush();
            
            return $this->json(array(
                'statut' => true
            ));
        }
        
        return $this->render('saison/ajax_ajouter_depuis_serie.html.twig', array(
            'serie' => $serie
        ));
    }
}
