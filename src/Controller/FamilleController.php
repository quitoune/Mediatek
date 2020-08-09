<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Famille;
use App\Entity\Lieu;

class FamilleController extends AppController
{

    /**
     *
     * @Route("/famille", name="famille")
     */
    public function index()
    {
        return $this->render('famille/index.html.twig', [
            'controller_name' => 'FamilleController'
        ]);
    }

    /**
     *
     * @Route("/famille/check_key/{key}", name="famille_check_key")
     *
     * @param string $clef
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function verifierClef(string $key = "")
    {
        $repo = $this->getDoctrine()->getRepository(Famille::class);
        $famille = $repo->findOneBy(array(
            'clef' => $key
        ));

        if (! is_null($famille)) {
            return $this->render('personne/inscription_famille.html.twig', array(
                'clef' => true,
                'lieux' => $famille->getLieux()
            ));
        } else {
            return $this->render('personne/inscription_famille.html.twig', array(
                'clef' => false
            ));
        }
    }
}
