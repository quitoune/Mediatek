<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Livre;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\LivreType;
use App\Entity\Personne;
use App\Entity\Format;
use App\Entity\LivrePersonne;
use App\Entity\Lieu;
use Symfony\Component\Form\FormError;
use App\Entity\Photo;

class LivreController extends AppController
{

    /**
     * Liste des livres
     *
     * @Route("/livre/liste/{page}", name="livre_liste")
     * @IsGranted("ROLE_UTILISATEUR")
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
     * @IsGranted("ROLE_UTILISATEUR")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajouter(Request $request, SessionInterface $session)
    {
        $livre = new Livre();

        $form = $this->createForm(LivreType::class, $livre);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $file = $request->files->get('livre')['photo']['chemin'];
            $nom = $request->request->all()['livre']['photo']['nom'];
            $fileName = $this->uploadFile($file, $nom);
            
            $photo = new Photo();
            
            $manager = $this->getDoctrine()->getManager();
            
            if ($fileName) {
                $photo->setChemin($fileName);
                $photo->setNom($nom);
                
                $livre->setPhoto($photo);
                
                $manager->persist($photo);
            } else {
                $form->addError(new FormError("L'image n'est pas au format autorisé (" . implode(', ', AppController::FORMAT_IMAGE) . ")."));
            }
            
            if($form->isValid()) {
                
                $slug = $this->createSlug($livre->getTitreOriginal(), 'Livre');
                $livre->setSlug($slug);
                
                if(isset($request->request->all()['livre']['livrePersonnes'])){
                    $livrePersonnes = $request->request->all()['livre']['livrePersonnes'];
                    foreach($livrePersonnes as $livrePersonne){
                        $repo_lieu = $this->getDoctrine()->getRepository(Lieu::class);
                        $repo_format = $this->getDoctrine()->getRepository(Format::class);
                        $repo_pers = $this->getDoctrine()->getRepository(Personne::class);
                        
                        $livre_personne = new LivrePersonne();
                        
                        $format = $repo_format->findOneBy(array(
                            'id' => $livrePersonne['format']
                        ));
                        
                        $lieu = $repo_lieu->findOneBy(array(
                            'id' => $livrePersonne['lieu']
                        ));
                        
                        $personne = $repo_pers->findOneBy(array(
                            'id' => $livrePersonne['personne']
                        ));
                        
                        $livre_personne->setLieu($lieu);
                        $livre_personne->setFormat($format);
                        $livre_personne->setPersonne($personne);
                        if($livrePersonne['date_achat']['day'] && $livrePersonne['date_achat']['month'] && $livrePersonne['date_achat']['year']){
                            $date  = $livrePersonne['date_achat']['year'] . "-";
                            $date .= ($livrePersonne['date_achat']['month'] < 10 ? "0" : "") . $livrePersonne['date_achat']['month'] . "-";
                            $date .= ($livrePersonne['date_achat']['day'] < 10 ? "0" : "") . $livrePersonne['date_achat']['day'];
                            $livre_personne->setDateAchat(new \DateTime($date));
                        }
                        $livre_personne->setIsbn($livrePersonne['isbn']);
                        $livre_personne->setLivre($livre);
                        $manager->persist($livre_personne);
                    }
                }
    
                $manager->persist($livre);
                $manager->flush();
    
                return $this->redirectToRoute('livre_afficher', array(
                    'slug' => $livre->getSlug()
                ));
            }
        }
        
        $personnes = array();
        foreach ($session->get('personnes') as $pers){
            $personnes[$pers['id']] = $pers['username'];
        }
        
        $forms = $this->getDoctrine()->getRepository(Format::class)->getFormatsForBooks();
        $formats = array();
        foreach($forms as $format){
            $formats[$format->getId()] = $format->getNom();
        }
        
        $lieux = array();
        foreach ($session->get('lieux') as $lieu){
            $lieux[$lieu['id']] = $lieu['nom'];
        }

        $paths = array(
            'urls' => array(
                $this->generateUrl('livre_liste') => 'Livres'
            ),
            'active' => 'Ajouter un livre'
        );

        return $this->render('livre/ajouter.html.twig', array(
            'form' => $form->createView(),
            'personnes' => json_encode($personnes, JSON_UNESCAPED_UNICODE),
            'formats' => json_encode($formats, JSON_UNESCAPED_UNICODE),
            'lieux' => json_encode($lieux, JSON_UNESCAPED_UNICODE),
            'paths' => $paths
        ));
    }
    
    /**
     * Formulaire de modification d'un livre
     *
     * @Route("/livre/{slug}/modifier/{page}", name="livre_modifier")
     * @IsGranted("ROLE_UTILISATEUR")
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
     * @IsGranted("ROLE_SUPER_ADMIN")
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
