<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Livre;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\LivreType;

class LivreController extends AppController
{

    /**
     * Liste des livres
     *
     * @Route("/livre/liste/{page}", name="livre_liste")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(SessionInterface $session, int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Livre::class);
        if ($session->get('user')['livre_vo']) {
            $order = 'Livre.titre_original';
        } else {
            $order = 'Livre.titre';
        }

        $params = array(
            'repository' => 'Livre',
            'orderBy' => array(
                $order => 'ASC'
            )
        );
        $livres = $repository->findAllElements($page, self::MAX_RESULT, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'livre_liste',
            'nbr_page' => ceil($livres['total'] / self::MAX_RESULT),
            'total' => $livres['total'],
            'route_params' => array()
        );

        $paths = array(
            'active' => 'Livres'
        );

        return $this->render('livre/index.html.twig', array(
            'livres' => $livres['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }

    /**
     *
     * @Route("/livre/{slug}/afficher/{page}", name="livre_afficher")
     *
     * @param Livre $livre
     * @param int $page
     */
    public function afficher(Livre $livre, int $page = 1)
    {
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('livre_liste') => 'Livres'
            ),
            'active' => 'Affichage' . $this->getIdNom($livre, 'livre')
        );

        return $this->render('livre/afficher.html.twig', array(
            'livre' => $livre,
            'page' => $page,
            'paths' => $paths
        ));
    }

    /**
     * Formulaire d'ajout d'un livre
     *
     * @Route("/livre/ajouter", name="livre_ajouter")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request, SessionInterface $session)
    {
        $livre = new Livre();

        $form = $this->createForm(LivreType::class, $livre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            
            $slug = $this->createSlug($livre->getTitreOriginal(), 'Livre');
            $livre->setSlug($slug);

            $manager->persist($livre);
            $manager->flush();

            return $this->redirectToRoute('livre_afficher', array(
                'slug' => $livre->getSlug()
            ));
        }

        $paths = array(
            'urls' => array(
                $this->generateUrl('livre_liste') => 'Livres'
            ),
            'active' => 'Ajouter un livre'
        );

        return $this->render('livre/ajouter.html.twig', array(
            'form' => $form->createView(),
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'un livre
     *
     * @Route("/livre/{slug}/modifier/{page}", name="livre_modifier")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, SessionInterface $session, Livre $livre, int $page = 1)
    {
        $form = $this->createForm(LivreType::class, $livre);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager = $this->getDoctrine()->getManager();
            
            $manager->persist($livre);
            $manager->flush();
            
            return $this->redirectToRoute('livre_afficher', array(
                'slug' => $livre->getSlug()
            ));
        }
        
        $paths = array(
            'urls' => array(
                $this->generateUrl('livre_liste', array(
                    'page' => $page
                )) => 'Livres',
                $this->generateUrl('livre_afficher', array(
                    'slug' => $livre->getSlug(),
                    'page' => $page
                )) => $livre->getTitreComplet($session->get('user')['livre_vo'])
            ),
            'active' => 'Modification' . $this->getIdNom($livre, 'livre')
        );
        
        return $this->render('livre/modifier.html.twig', array(
            'form' => $form->createView(),
            'livre' => $livre,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de suppression d'un livre
     *
     * @Route("/livre/supprimer/{slug}", name="livre_supprimer")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Livre $livre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Livre $livre){
        $manager = $this->getDoctrine()->getManager();
        
        $livrePersonnes = $livre->getLivrePersonnes();
        foreach($livrePersonnes as $livrePersonne){
            $manager->remove($livrePersonne);
        }
        
        $manager->remove($livre);
        $manager->flush();
        
        return $this->redirectToRoute('livre_liste');
    }
}
