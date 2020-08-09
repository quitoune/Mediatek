<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Saison;
use App\Entity\ActeurSaison;
use App\Entity\Acteur;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ActeurSaisonController extends AppController
{
    /**
     * Rôle d'un acteur dans une saison
     *
     * @var array
     */
    const ROLE = array(
        0 => 'Invité',
        1 => 'Récurrent',
        2 => 'Principal'
    );
    
    /**
     * @Route("/acteur_saison", name="acteur_saison")
     */
    public function index()
    {
        return $this->render('acteur_saison/index.html.twig', [
            'controller_name' => 'ActeurSaisonController',
        ]);
    }
    
    /**
     * Affichage des acteurs principaux | récurrents | invités d'une saison
     * 
     * @Route("/acteur_saison/{slug}/afficher_pour_saison/{principal}", name="acteur_pour_saison")
     * 
     * @param Saison $saison
     * @param int $principal
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherActeurPourSaison(Saison $saison, int $principal){
        $repo = $this->getDoctrine()->getRepository(ActeurSaison::class);
        switch($principal){
            case 2:
                $principaux = $repo->getActeurByRole($saison->getId(), $principal);
                return $this->render('acteur_saison/afficher_principaux_saison.html.twig', array(
                    'saison' => $saison,
                    'principaux' => $principaux
                ));
                break;
            case 1:
                $recurrents = $repo->getActeurByRole($saison->getId(), $principal);
                return $this->render('acteur_saison/afficher_recurrents_saison.html.twig', array(
                    'saison' => $saison,
                    'recurrents' => $recurrents
                ));
                break;
            case 0:
                $invites = $repo->getActeurByRole($saison->getId(), $principal);
                return $this->render('acteur_saison/afficher_invites_saison.html.twig', array(
                    'saison' => $saison,
                    'invites' => $invites
                ));
                break;
            default:
                break;
        }
    }
    
    /**
     * Affichage des séries d'un acteur
     *
     * @Route("/acteur_saison/{slug}/afficher_pour_acteur/{page}", name="saison_pour_acteur")
     *
     * @param Acteur $acteur
     * @param int $principal
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherSaisonPourActeur(Acteur $acteur, int $page = 1){
        $repository = $this->getDoctrine()->getRepository(ActeurSaison::class);
        
        $params = array(
            'repository' => 'ActeurSaison',
            'orderBy' => array(
                'serie.titre_original' => 'ASC',
                'saison.numero_saison' => 'ASC'
            ),
            'condition' => array(
                'ActeurSaison.acteur = ' . $acteur->getId()
            ),
            'jointure' => array(
                'ActeurSaison' => 'saison',
                'saison' => 'serie'
            )
        );
        $acteurSaisons = $repository->findAllElements($page, self::MAX_AJAX_RESULT, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'saison_pour_acteur',
            'nbr_page' => ceil($acteurSaisons['total'] / self::MAX_AJAX_RESULT),
            'total' => $acteurSaisons['total'],
            'route_params' => array(
                'slug' => $acteur->getSlug()
            )
        );
        
        return $this->render('acteur_saison/afficher_pour_acteur.html.twig', array(
            'bloc' => '#saisons',
            'acteurSaisons' => $acteurSaisons['paginator'],
            'acteur' => $acteur,
            'pagination' => $pagination
        ));
    }
    
    /**
     *
     * @Route("/acteur_saison/{slug}/ajax_editer_saisons_acteur", name="ajax_editer_saisons_acteur")
     *
     * @param Acteur $acteur
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editerSaisonPourActeur(Request $request, Acteur $acteur, SessionInterface $session)
    {
        $repository = $this->getDoctrine()->getRepository(ActeurSaison::class);
        $repo = $this->getDoctrine()->getRepository(Saison::class);
        $repo_saisons = $repo->findBy(array(), array(
            'serie' => 'ASC',
            'numero_saison' => 'ASC'
        ));
        
        if (count($request->request->all())) {
            //             $this->pre($request->request->all()['acteur']['acteurSaisons']);
            //             die();
            $acteurSaisons = $request->request->all()['acteur']['acteurSaisons'];
            
            $manager = $this->getDoctrine()->getManager();
            
            foreach ($acteurSaisons as $acteurSaison) {
                $acteur_saison = new ActeurSaison();
                if ($acteurSaison['delete']) {
                    $acteur_saison = $repository->findOneBy(array(
                        'id' => $acteurSaison['id']
                    ));
                    $manager->remove($acteur_saison);
                } else if (isset($acteurSaison['id'])) {
                    $acteur_saison = $repository->findOneBy(array(
                        'id' => $acteurSaison['id']
                    ));
                    if ($acteurSaison['role']) {
                        $acteur_saison->setRole($acteurSaison['role']);
                    } else {
                        $acteur_saison->setRole(NULL);
                    }
                    $acteur_saison->setPrincipal($acteurSaison['principal']);
                    $acteur_saison->setSaison($repo->findOneBy(array(
                        'id' => $acteurSaison['saison']
                    )));
                    $acteur_saison->setActeur($acteur);
                    
                    $manager->persist($acteur_saison);
                } else {
                    if ($acteurSaison['role']) {
                        $acteur_saison->setRole($acteurSaison['role']);
                    } else {
                        $acteur_saison->setRole(NULL);
                    }
                    $acteur_saison->setPrincipal($acteurSaison['principal']);
                    $acteur_saison->setSaison($repo->findOneBy(array(
                        'id' => $acteurSaison['saison']
                    )));
                    $acteur_saison->setActeur($acteur);
                    
                    $manager->persist($acteur_saison);
                }
            }
            $manager->flush();
            
            return $this->json(array(
                'statut' => true
            ));
        }
        
        $opt_saison = array(
            "class" => "form-control",
            "id" => "acteur_acteurSaisons_INDEX_saison",
            "default" => 1
        );
        
        $saisons = array();
        
        $index = 0;
        foreach ($repo_saisons as $saison) {
            if ($index == 0) {
                $opt_saison["default"] = $saison->getId();
            }
            $saisons[$saison->getId()] = $saison->getNomComplet($session->get('user')['episode_vo']);
            $index ++;
        }
        
        return $this->render('acteur_saison/ajax_editer_saisons_acteur.html.twig', array(
            'opt_saison' => $opt_saison,
            'acteur' => $acteur,
            'saisons' => $saisons
        ));
    }
    
    /**
     *
     * @Route("/acteur_saison/{slug}/ajax_editer_acteurs_saison", name="ajax_editer_acteurs_saison")
     *
     * @param Saison $saison
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editerActeurPourSaison(Request $request, Saison $saison, SessionInterface $session)
    {
        $repository = $this->getDoctrine()->getRepository(ActeurSaison::class);
        $repo = $this->getDoctrine()->getRepository(Acteur::class);
        $repo_acteurs = $repo->findBy(array(), array(
            'nom' => 'ASC',
            'prenom' => 'ASC',
            'nom_usage' => 'ASC'
        ));
        
        if (count($request->request->all())) {
            //             $this->pre($request->request->all()['saison']['acteurSaisons']);
            //             die();
            $acteurSaisons = $request->request->all()['saison']['acteurSaisons'];
            
            $manager = $this->getDoctrine()->getManager();
            
            foreach ($acteurSaisons as $acteurSaison) {
                $acteur_saison = new ActeurSaison();
                if ($acteurSaison['delete']) {
                    $acteur_saison = $repository->findOneBy(array(
                        'id' => $acteurSaison['id']
                    ));
                    $manager->remove($acteur_saison);
                } else if (isset($acteurSaison['id'])) {
                    $acteur_saison = $repository->findOneBy(array(
                        'id' => $acteurSaison['id']
                    ));
                    if ($acteurSaison['role']) {
                        $acteur_saison->setRole($acteurSaison['role']);
                    } else {
                        $acteur_saison->setRole(NULL);
                    }
                    $acteur_saison->setPrincipal($acteurSaison['principal']);
                    $acteur_saison->setActeur($repo->findOneBy(array(
                        'id' => $acteurSaison['acteur']
                    )));
                    $acteur_saison->setSaison($saison);
                    
                    $manager->persist($acteur_saison);
                } else {
                    if ($acteurSaison['role']) {
                        $acteur_saison->setRole($acteurSaison['role']);
                    } else {
                        $acteur_saison->setRole(NULL);
                    }
                    $acteur_saison->setPrincipal($acteurSaison['principal']);
                    $acteur_saison->setActeur($repo->findOneBy(array(
                        'id' => $acteurSaison['acteur']
                    )));
                    $acteur_saison->setSaison($saison);
                    
                    $manager->persist($acteur_saison);
                }
            }
            $manager->flush();
            
            return $this->json(array(
                'statut' => true
            ));
        }
        
        $opt_acteur = array(
            "class" => "form-control",
            "id" => "saison_acteurSaisons_INDEX_acteur",
            "default" => 1
        );
        
        $acteurs = array();
        
        $index = 0;
        foreach ($repo_acteurs as $acteur) {
            if ($index == 0) {
                $opt_acteur["default"] = $acteur->getId();
            }
            $acteurs[$acteur->getId()] = $acteur->getNomComplet();
            $index ++;
        }
        
        return $this->render('acteur_saison/ajax_editer_acteurs_saison.html.twig', array(
            'opt_acteur' => $opt_acteur,
            'saison' => $saison,
            'acteurs' => $acteurs
        ));
    }
}
