<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Personne;
use App\Entity\Film;
use App\Entity\Photo;
use App\Entity\Livre;
use App\Entity\Episode;

class IndexController extends AppController
{
    /**
     *
     * @Route("/", name="index")
     * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     */
    public function index()
    {
        $mois = 3;
        
        $personnes = $this->getDoctrine()
            ->getRepository(Personne::class)
            ->getMembreAssocie($this->getPersonne()
            ->getId());
        $pers = array();
        foreach ($personnes as $personne) {
            $pers[] = $personne['id'];
        }
        
        $films = $this->getDoctrine()
            ->getRepository(Film::class)
            ->getRecentFilm($pers, "-$mois months");

        foreach ($films as $key => $film) {
            $films[$key]['photo_id'] = $this->getDoctrine()
                ->getRepository(Photo::class)
                ->findOneBy(array(
                'id' => $film['photo_id']
            ));
        }
        
        $livres = $this->getDoctrine()
        ->getRepository(Livre::class)
        ->getRecentLivre($pers, "-$mois months");
        
        foreach ($livres as $key => $livre) {
            $livres[$key]['photo_id'] = $this->getDoctrine()
            ->getRepository(Photo::class)
            ->findOneBy(array(
                'id' => $livre['photo_id']
            ));
        }
        
        $episodes = $this->getDoctrine()
        ->getRepository(Episode::class)
        ->getRecentEpisode($pers, "-$mois months");
        
        foreach ($episodes as $key => $episode) {
            $episodes[$key]['photo_id'] = $this->getDoctrine()
            ->getRepository(Photo::class)
            ->findOneBy(array(
                'id' => $episode['photo_id']
            ));
        }

        return $this->render('index/index.html.twig', array(
            'doctrine' => $this->getDoctrine(),
            'mois' => $mois,
            'films' => $films,
            'livres' => $livres,
            'episodes' => $episodes
        ));
    }
}
