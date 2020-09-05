<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Livre;
use App\Entity\LivrePersonne;
use App\Form\LivrePersonneType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Lieu;
use App\Entity\Personne;

class LivrePersonneController extends AppController
{

    /**
     *
     * @Route("/livre_personne", name="livre_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     */
    public function index()
    {
        return $this->render('livre_personne/index.html.twig', [
            'controller_name' => 'LivrePersonneController'
        ]);
    }

    /**
     * Affichage des propriétaires d'un livre
     *
     * @Route("/livre_personne/{slug}/afficher_pour_livre", name="personne_pour_livre")
     *
     * @param SessionInterface $session
     * @param Livre $livre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherProprios(SessionInterface $session, Livre $livre)
    {
        $repo = $this->getDoctrine()->getRepository(LivrePersonne::class);
        $personnes = array();
        foreach ($session->get('personnes') as $personne) {
            $personnes[] = $personne["id"];
        }
        return $this->render('livre_personne/afficher_proprios_livre.html.twig', array(
            'livre' => $livre,
            'livrePersonnes' => $repo->getLivrePersonnes($livre->getId(), $personnes)
        ));
    }

    /**
     * Ajout d'un propriétaire à un livre
     *
     * @Route("/livre_personne/{slug}/ajax_ajouter_personne", name="ajax_livre_personne_ajouter_personne")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Livre $livre
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouterDepuisLivre(SessionInterface $session, Request $request, Livre $livre)
    {
        $livrePersonne = new LivrePersonne();
        $livrePersonne->setLivre($livre);

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

        $form = $this->createForm(LivrePersonneType::class, $livrePersonne, array(
            'avec_livre' => false,
            'select_personne' => $select_personne,
            'select_lieu' => $select_lieu
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($livrePersonne);
            $manager->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('livre_personne/ajax_ajouter_personne.html.twig', array(
            'form' => $form->createView(),
            'livre' => $livre
        ));
    }

    /**
     * Edition lien personne - livre depuis livre
     *
     * @Route("/livre_personne/{id}/ajax_editer_personne", name="ajax_livre_personne_editer_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param LivrePersonne $livrePersonne
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editerDepuisLivre(SessionInterface $session, Request $request, LivrePersonne $livrePersonne)
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

        $form = $this->createForm(LivrePersonneType::class, $livrePersonne, array(
            'avec_livre' => false,
            'select_personne' => $select_personne,
            'select_lieu' => $select_lieu
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($livrePersonne);
            $manager->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('livre_personne/ajax_editer_personne.html.twig', array(
            'form' => $form->createView(),
            'livrePersonne' => $livrePersonne
        ));
    }

    /**
     * Suppression lien personne - livre depuis livre
     *
     * @Route("/livre_personne/{id}/ajax_supprimer_personne", name="ajax_livre_personne_supprimer_personne")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param LivrePersonne $livrePersonne
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function supprimerDepuisLivre(LivrePersonne $livrePersonne)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($livrePersonne);
        $manager->flush();

        return $this->json(array(
            'statut' => true
        ));
    }
}
