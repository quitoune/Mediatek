<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Format;
use App\Form\FormatType;

class FormatController extends AppController
{
    /**
     * Liste des formats
     *
     * @Route("/format/liste/{page}", name="format_liste")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Format::class);
        
        $params = array(
            'repository' => 'Format',
            'orderBy' => array(
                'Format.nom' => 'ASC'
            )
        );
        $formats = $repository->findAllElements($page, self::MAX_RESULT, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'format_liste',
            'nbr_page' => ceil($formats['total'] / self::MAX_RESULT),
            'total' => $formats['total'],
            'route_params' => array()
        );
        
        $paths = array(
            'active' => 'Formats'
        );
        
        return $this->render('format/index.html.twig', array(
            'formats' => $formats['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire d'ajout d'un format
     *
     * @Route("/format/ajouter", name="format_ajouter")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request)
    {
        $format = new Format();
        
        $form = $this->createForm(FormatType::class, $format);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager = $this->getDoctrine()->getManager();
            
            $manager->persist($format);
            $manager->flush();
            
            return $this->redirectToRoute('format_liste');
        }
        
        $paths = array(
            'urls' => array(
                $this->generateUrl('format_liste') => 'Formats'
            ),
            'active' => 'Ajouter un format'
        );
        
        return $this->render('format/ajouter.html.twig', array(
            'form' => $form->createView(),
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'un format
     *
     * @Route("/format/modifier/{id}/{page}", name="format_modifier")
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @param Format $format
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, Format $format, int $page = 1)
    {
        $form = $this->createForm(FormatType::class, $format);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $format = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($format);
            $manager->flush();
            
            return $this->redirectToRoute('format_liste', array(
                'page' => $page
            ));
        }
        
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('format_liste', array(
                    'page' => $page
                )) => 'Formats'
            ),
            'active' => 'Modification' . $this->getIdNom($format, 'format')
        );
        
        return $this->render('format/modifier.html.twig', array(
            'form' => $form->createView(),
            'format' => $format,
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de suppression d'un format
     *
     * @Route("/format/supprimer/{id}", name="format_supprimer")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * 
     * @param Format $format
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Format $format){
        $manager = $this->getDoctrine()->getManager();
        
        $livre_personnes = $format->getLivrePersonnes();
        foreach ($livre_personnes as $livre_personne){
            $format->removeLivrePersonne($livre_personne);
        }
        
        $film_personnes = $format->getFilmPersonnes();
        foreach ($film_personnes as $film_personne){
            $format->removeFilmPersonne($film_personne);
        }
        
        $episode_personnes = $format->getEpisodePersonnes();
        foreach ($episode_personnes as $episode_personne){
            $format->removeEpisodePersonne($episode_personne);
        }
        
        $manager->persist($format);
        
        $manager->remove($format);
        $manager->flush();
        
        return $this->redirectToRoute('format_liste');
    }
}
