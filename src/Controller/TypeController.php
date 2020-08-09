<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Type;
use App\Form\TypeType;

class TypeController extends AppController
{
    /**
     * Liste des types
     *
     * @Route("/type/liste/{page}", name="type_liste")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Type::class);
        
        $params = array(
            'repository' => 'Type',
            'orderBy' => array(
                'Type.nom' => 'ASC'
            )
        );
        $types = $repository->findAllElements($page, self::MAX_RESULT, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'type_liste',
            'nbr_page' => ceil($types['total'] / self::MAX_RESULT),
            'total' => $types['total'],
            'route_params' => array()
        );
        
        $paths = array(
            'active' => 'Types'
        );
        
        return $this->render('type/index.html.twig', array(
            'types' => $types['paginator'],
            'pagination' => $pagination,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire d'ajout d'un type
     *
     * @Route("/type/ajouter", name="type_ajouter")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request)
    {
        $type = new Type();
        
        $form = $this->createForm(TypeType::class, $type);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager = $this->getDoctrine()->getManager();
            
            $manager->persist($type);
            $manager->flush();
            
            return $this->redirectToRoute('type_liste');
        }
        
        $paths = array(
            'urls' => array(
                $this->generateUrl('type_liste') => 'Types'
            ),
            'active' => 'Ajouter un type'
        );
        
        return $this->render('type/ajouter.html.twig', array(
            'form' => $form->createView(),
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'un type
     *
     * @Route("/type/modifier/{id}/{page}", name="type_modifier")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     *
     * @param Request $request
     * @param Type $type
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifier(Request $request, Type $type, int $page = 1)
    {
        $form = $this->createForm(TypeType::class, $type);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($type);
            $manager->flush();
            
            return $this->redirectToRoute('type_liste', array(
                'page' => $page
            ));
        }
        
        $paths = array(
            'home' => $this->homeURL(),
            'urls' => array(
                $this->generateUrl('type_liste', array(
                    'page' => $page
                )) => 'Types'
            ),
            'active' => 'Modification' . $this->getIdNom($type, 'type')
        );
        
        return $this->render('type/modifier.html.twig', array(
            'form' => $form->createView(),
            'type' => $type,
            'page' => $page,
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de suppression d'un type
     *
     * @Route("/type/supprimer/{id}", name="type_supprimer")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Type $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimer(Type $type){
        $manager = $this->getDoctrine()->getManager();
        
        $livres = $type->getLivres();
        foreach ($livres as $livre){
            $type->removeLivre($livre);
        }
        
        $manager->persist($type);
        
        $manager->remove($type);
        $manager->flush();
        
        return $this->redirectToRoute('type_liste');
    }
}
