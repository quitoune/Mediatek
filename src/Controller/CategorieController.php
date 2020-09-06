<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categorie;
use App\Form\CategorieType;

class CategorieController extends AppController
{

    /**
     * Liste des catégories
     *
     * @Route("/categorie/liste/{page}", name="categorie_liste")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Categorie::class);

        $params = array(
            'repository' => 'Categorie',
            'orderBy' => array(
                'Categorie.nom' => 'ASC'
            )
        );
        $categories = $repository->findAllElements($page, self::MAX_RESULT, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'categorie_liste',
            'nbr_page' => ceil($categories['total'] / self::MAX_RESULT),
            'total' => $categories['total'],
            'route_params' => array()
        );

        $paths = array(
            'active' => 'Catégories'
        );

        return $this->render('categorie/index.html.twig', array(
            'categories' => $categories['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }

    /**
     * Formulaire d'ajout d'une catégorie
     *
     * @Route("/categorie/ajouter", name="categorie_ajouter")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request)
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie, array(
            'label_submit' => 'Ajouter'
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($categorie);
            $manager->flush();

            return $this->redirectToRoute('categorie_liste');
        }

        $paths = array(
            'urls' => array(
                $this->generateUrl('categorie_liste') => 'Catégories'
            ),
            'active' => 'Ajouter une catégorie'
        );

        return $this->render('categorie/ajouter.html.twig', array(
            'form' => $form->createView(),
            'paths' => $paths
        ));
    }

    /**
     * Formulaire de modification d'une catégorie
     *
     * @Route("/categorie/modifier/{id}/{page}", name="categorie_modifier")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param Categorie $categorie
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, Categorie $categorie, int $page = 1)
    {
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($categorie);
            $manager->flush();

            return $this->redirectToRoute('categorie_liste', array(
                'page' => $page
            ));
        }

        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('categorie_liste', array(
                    'page' => $page
                )) => 'Categories'
            ),
            'active' => 'Modification' . $this->getIdNom($categorie, 'categorie')
        );

        return $this->render('categorie/modifier.html.twig', array(
            'form' => $form->createView(),
            'categorie' => $categorie,
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de suppression d'une catégorie
     *
     * @Route("/categorie/supprimer/{id}", name="categorie_supprimer")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Categorie $categorie){
        $manager = $this->getDoctrine()->getManager();
        
        $livres = $categorie->getLivres();
        foreach ($livres as $livre){
            $categorie->removeLivre($livre);
        }
        
        $films = $categorie->getFilms();
        foreach ($films as $film){
            $categorie->removeFilm($film);
        }
        
        $series = $categorie->getSeries();
        foreach ($series as $serie){
            $categorie->removeSeries($serie);
        }
        
        $manager->persist($categorie);
        
        $manager->remove($categorie);
        $manager->flush();
        
        return $this->redirectToRoute('categorie_liste');
    }
}
