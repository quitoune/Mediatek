<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Personne;
use App\Form\PersonneType;
use App\Entity\LivrePersonne;
use App\Entity\FilmPersonne;
use App\Entity\EpisodePersonne;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PersonneController extends AppController
{

    /**
     * Liste des membres
     *
     * @Route("/personne/liste/{role}/{page}", name="annuaire")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param SessionInterface $session
     * @param int $page
     * @param string $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(SessionInterface $session, int $page = 1, string $role)
    {
        $repository = $this->getDoctrine()->getRepository(Personne::class);

        $personnes = array();
        foreach ($session->get('personnes') as $pers) {
            $personnes[] = $pers["id"];
        }

        switch ($role) {
            case 'super_admin':
                $params = array(
                    'repository' => 'Personne',
                    'orderBy' => array(
                        'Personne.nom' => 'ASC',
                        'Personne.prenom' => 'ASC',
                        'Personne.username' => 'ASC'
                    )
                );
                break;
            case 'admin':
            case 'utilisateur':
                $params = array(
                    'repository' => 'Personne',
                    'condition' => array(
                        'Personne.id IN (' . implode(', ', $personnes) . ')'
                    ),
                    'orderBy' => array(
                        'Personne.nom' => 'ASC',
                        'Personne.prenom' => 'ASC',
                        'Personne.username' => 'ASC'
                    )
                );
                break;
        }
        $personnes = $repository->findAllElements($page, self::MAX_RESULT, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'annuaire',
            'nbr_page' => ceil($personnes['total'] / self::MAX_RESULT),
            'total' => $personnes['total'],
            'route_params' => array(
                'role' => $role
            )
        );

        $paths = array(
            'active' => 'Annuaire'
        );

        return $this->render('personne/index.html.twig', array(
            'personnes' => $personnes['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }

    /**
     *
     * @Route("/inscription", name="inscription")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function inscription(Request $request)
    {
        $personne = new Personne();

        $form = $this->createForm(PersonneType::class, $personne, array(
            'avec_username' => true
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->pre($request->request->all());
            die();
            $personne = $form->getData();

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($personne);
            $manager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('personne/inscription.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/inscription/inscription_famille/{cas}/{clef}", name="inscription_famille")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function inscription_famille(int $cas, string $clef = "")
    {
        switch ($cas) {
            case 1:
                return $this->render('personne/inscription_ajout_famille.html.twig');
                break;
            case 0:
                return $this->render('personne/inscription_rejoindre_famille.html.twig');
                break;
        }
    }

    /**
     *
     * @Route("/inscription/inscription_lieu/{clef}", name="inscription_lieu")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function inscription_lieu(int $cas)
    {
        switch ($cas) {
            case 1:
                return $this->render('personne/inscription_ajout_famille.html.twig');
                break;
            case 0:
                return $this->render('personne/inscription_rejoindre_famille.html.twig');
                break;
        }
    }

    /**
     * Page Mon Compte
     *
     * @Route("/personne/{username}/afficher", name="personne_afficher")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Personne $personne
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficher(Personne $personne)
    {
        $nbr_livre = count($this->getDoctrine()
            ->getRepository(LivrePersonne::class)
            ->createQueryBuilder('LivrePersonne')
            ->select('COUNT(LivrePersonne.id)')
            ->join('LivrePersonne.personne', 'personne')
            ->andWhere('personne.id = ' . $personne->getId())
            ->groupBy('LivrePersonne.livre')
            ->getQuery()
            ->getResult());

        $nbr_film = count($this->getDoctrine()
            ->getRepository(FilmPersonne::class)
            ->createQueryBuilder('FilmPersonne')
            ->select('COUNT(FilmPersonne.id)')
            ->join('FilmPersonne.personne', 'personne')
            ->andWhere('personne.id = ' . $personne->getId())
            ->groupBy('FilmPersonne.film')
            ->getQuery()
            ->getResult());

        $nbr_episode = count($this->getDoctrine()
            ->getRepository(EpisodePersonne::class)
            ->createQueryBuilder('EpisodePersonne')
            ->select('COUNT(EpisodePersonne.id)')
            ->join('EpisodePersonne.personne', 'personne')
            ->andWhere('personne.id = ' . $personne->getId())
            ->groupBy('EpisodePersonne.episode')
            ->getQuery()
            ->getResult());

        $nbr_serie = count($this->getDoctrine()
            ->getRepository(EpisodePersonne::class)
            ->createQueryBuilder('EpisodePersonne')
            ->select('COUNT(serie.id)')
            ->join('EpisodePersonne.personne', 'personne')
            ->join('EpisodePersonne.episode', 'episode')
            ->join('episode.saison', 'saison')
            ->join('saison.serie', 'serie')
            ->andWhere('personne.id = ' . $personne->getId())
            ->groupBy('serie.id')
            ->getQuery()
            ->getResult());

        return $this->render('personne/afficher.html.twig', array(
            'personne' => $personne,
            'nbr_livre' => $nbr_livre,
            'nbr_serie' => $nbr_serie,
            'nbr_film' => $nbr_film,
            'nbr_episode' => $nbr_episode
        ));
    }

    /**
     * Modifier un utilisateur
     *
     * @Route("/personne/{username}/modifier/{page}", name="personne_modifier")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param Personne $personne
     * @param int $page
     */
    public function modifier(Request $request, Personne $personne, int $page = 1)
    {
        $form = $this->createForm(PersonneType::class, $personne, array(
            'label_submit' => 'Mise à jour'
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($personne);
            $manager->flush();

            return $this->redirectToRoute('personne_afficher', array(
                'username' => $personne->getUsername(),
                'page' => $page
            ));
        }

        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('personne_liste', array(
                    'page' => $page
                )) => 'Membres',
                $this->generateUrl('personne_afficher', array(
                    'username' => $personne->getUsername(),
                    'page' => $page
                )) => $personne->getNomComplet()
            ),
            'active' => 'Modification' . $this->getIdNom($personne, 'personne')
        );

        return $this->render('personne/modifier.html.twig', array(
            'form' => $form->createView(),
            'personne' => $personne,
            'page' => $page,
            'paths' => $paths
        ));
    }
}
