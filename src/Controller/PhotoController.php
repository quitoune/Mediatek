<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Photo;
use App\Form\PhotoType;
use Symfony\Component\Form\FormError;

class PhotoController extends AppController
{
    /**
     * Liste des photos
     *
     * @Route("/photo/liste/{page}", name="photo_liste")
     * @IsGranted("ROLE_UTILISATEUR")
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
     * @IsGranted("ROLE_UTILISATEUR")
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
    
    /**
     * Formulaire d'ajout d'une photo
     *
     * @Route("/photo/ajouter", name="photo_ajouter")
     * @IsGranted("ROLE_UTILISATEUR")
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request)
    {
        $photo = new Photo();
        
        $form = $this->createForm(PhotoType::class, $photo, array(
            'add' => true
        ));
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted()){
            $file = $request->files->get('photo')['chemin'];
            $nom = $request->request->all()['photo']['nom'];
            $fileName = $this->uploadFile($file, $nom);
            
            if ($fileName) {
                $photo->setChemin($fileName);
            } else {
                $form->addError(new FormError("L'image n'est pas au format autorisé (" . implode(', ', AppController::FORMAT_IMAGE) . ")."));
            }
        
            if($form->isValid()) {
                $photo = $form->getData();
                
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($photo);
                $manager->flush();
                
                return $this->redirectToRoute('photo_liste');
            }
        }
        
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('photo_liste', array(
                    'page' => 1
                )) => 'Galerie de photos'
            ),
            'active' => "Ajout d'une photo"
        );
        
        return $this->render('photo/ajouter.html.twig', array(
            'form' => $form->createView(),
            'photo' => $photo,
            'paths' => $paths
        ));
    }
    
    /**
     * Affichage d'une photo
     *
     * @Route("/photo/afficher/{id}", name="photo_afficher")
     * @IsGranted("ROLE_UTILISATEUR")
     * 
     * @param Photo $photo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    function afficher(Photo $photo){
        return $this->render('photo/afficher.html.twig', array(
            'photo' => $photo
        ));
    }
}
