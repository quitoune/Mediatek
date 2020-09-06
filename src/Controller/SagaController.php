<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Saga;
use App\Form\SagaType;

class SagaController extends AppController
{

    /**
     * Liste des sagas
     *
     * @Route("/saga/liste/{page}", name="saga_liste")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Saga::class);

        $params = array(
            'repository' => 'Saga',
            'orderBy' => array(
                'Saga.nom' => 'ASC'
            )
        );
        $sagas = $repository->findAllElements($page, self::MAX_RESULT, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'saga_liste',
            'nbr_page' => ceil($sagas['total'] / self::MAX_RESULT),
            'total' => $sagas['total'],
            'route_params' => array()
        );

        $paths = array(
            'active' => 'Sagas'
        );

        return $this->render('saga/index.html.twig', array(
            'sagas' => $sagas['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }

    /**
     * Affichage d'une saga
     *
     * @Route("/saga/{slug}/afficher/{page}", name="saga_afficher")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Saga $saga
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficher(Saga $saga, int $page = 1)
    {
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('saga_liste', array(
                    'page' => $page
                )) => 'Sagas'
            ),
            'active' => 'Affichage' . $this->getIdNom($saga, 'Saga')
        );

        return $this->render('saga/afficher.html.twig', array(
            'saga' => $saga,
            'page' => $page,
            'paths' => $paths
        ));
    }

    /**
     * Formulaire d'ajout d'une saga
     *
     * @Route("/saga/ajouter", name="saga_ajouter")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request)
    {
        $saga = new Saga();

        $form = $this->createForm(SagaType::class, $saga);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();

            $supp = "";
            if (! is_null($saga->getSaga())) {
                $supp = $saga->getSaga()->getSlug() . "-";
            }

            $slug = $this->createSlug($supp . $saga->getNom(), 'Saga');
            $saga->setSlug($slug);

            $manager->persist($saga);
            $manager->flush();

            return $this->redirectToRoute('saga_afficher', array(
                'slug' => $saga->getSlug()
            ));
        }

        $paths = array(
            'urls' => array(
                $this->generateUrl('saga_liste') => 'Sagas'
            ),
            'active' => 'Ajouter une saga'
        );

        return $this->render('saga/ajouter.html.twig', array(
            'form' => $form->createView(),
            'paths' => $paths
        ));
    }

    /**
     * Formulaire de modification d'un saga
     *
     * @Route("/saga/modifier/{id}/{page}", name="saga_modifier")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param Saga $saga
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, Saga $saga, int $page = 1)
    {
        $form = $this->createForm(SagaType::class, $saga);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            
            $supp = "";
            if (! is_null($saga->getSaga())) {
                $supp = $saga->getSaga()->getSlug() . "-";
            }
            
            $slug = $this->createSlug($supp . $saga->getNom(), 'Saga', "", $saga->getId());
            $saga->setSlug($slug);
            
            $manager->persist($type);
            $manager->flush();

            return $this->redirectToRoute('saga_afficher', array(
                'slug' => $saga->getSlug(),
                'page' => $page
            ));
        }

        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('saga_liste', array(
                    'page' => $page
                )) => 'Sagas',
                $this->generateUrl('saga_afficher', array(
                    'slug' => $saga->getSlug(),
                    'page' => $page
                )) => $saga->getNom()
            ),
            'active' => 'Modification' . $this->getIdNom($saga, 'saga')
        );

        return $this->render('saga/modifier.html.twig', array(
            'form' => $form->createView(),
            'saga' => $saga,
            'page' => $page,
            'paths' => $paths
        ));
    }

    /**
     * Formulaire de suppression d'une saga
     *
     * @Route("/saga/supprimer/{slug}", name="saga_supprimer")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param Saga $saga
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Saga $saga)
    {
        $manager = $this->getDoctrine()->getManager();

        $saga_mere = $saga->getSaga();
        $sousSagas = $saga->getSousSagas();

        if (is_null($saga_mere)) {
            foreach ($sousSagas as $sousSaga) {
                $sousSaga->setSaga(NULL);
                $slug = $this->createSlug($sousSaga->getNom(), 'Saga');
                $sousSaga->setSlug($slug);

                $manager->persist($sousSaga);
            }
        } else {
            foreach ($sousSagas as $sousSaga) {
                $sousSaga->setSaga($saga_mere);
                $supp = $saga_mere->getSlug() . "-";
                $slug = $this->createSlug($supp . $sousSaga->getNom(), 'Saga');
                $sousSaga->setSlug($slug);

                $manager->persist($sousSaga);
            }
        }
        
        foreach ($saga->getFilms() as $film){
            $film->setSaga(NULL);
            $manager->persist($film);
        }
        
        foreach ($saga->getSeries() as $serie){
            $serie->setSaga(NULL);
            $manager->persist($serie);
        }
        
        foreach ($saga->getLivres() as $livre){
            $livre->setSaga(NULL);
            $manager->persist($livre);
        }

        $manager->remove($saga);
        $manager->flush();

        return $this->redirectToRoute('saga_liste');
    }
}
