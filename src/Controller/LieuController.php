<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AppController
{
    /**
     * @Route("/lieu", name="lieu")
     */
    public function index()
    {
        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }
}
