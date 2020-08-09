<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Photo;
use App\Form\PhotoType;

class PhotoController extends AppController
{
    /**
     * Liste des photos
     *
     * @Route("/photo/liste/{page}", name="photo_liste")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Photo::class);
        
        $params = array(
            'repository' => 'Photo',
            'orderBy' => array(
                'Photo.id' => 'ASC'
            )
        );
        $photos = $repository->findAllElements($page, self::MAX_RESULT, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'photo_liste',
            'nbr_page' => ceil($photos['total'] / self::MAX_RESULT),
            'total' => $photos['total'],
            'route_params' => array()
        );
        
        $paths = array(
            'active' => 'Photos'
        );
        
        return $this->render('photo/index.html.twig', array(
            'photos' => $photos['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'une photo
     *
     * @Route("/photo/modifier/{id}/{page}", name="photo_modifier")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param Request $request
     * @param Photo $photo
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, Photo $photo, int $page = 1)
    {
        $form = $this->createForm(PhotoType::class, $photo);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($photo);
            $manager->flush();
            
            return $this->redirectToRoute('photo_liste', array(
                'page' => $page
            ));
        }
        
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('photo_liste', array(
                    'page' => $page
                )) => 'Galerie de photos'
            ),
            'active' => 'Modification' . $this->getIdNom($photo, 'photo')
        );
        
        return $this->render('photo/modifier.html.twig', array(
            'form' => $form->createView(),
            'photo' => $photo,
            'page' => $page,
            'paths' => $paths
        ));
    }
}
