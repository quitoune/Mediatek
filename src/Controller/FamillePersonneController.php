<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class FamillePersonneController extends AppController
{
    /**
     * @Route("/famille_personne", name="famille_personne")
     */
    public function index()
    {
        return $this->render('famille_personne/index.html.twig', [
            'controller_name' => 'FamillePersonneController',
        ]);
    }
}
