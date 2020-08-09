<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Avatar;

class AvatarController extends AbstractController
{
    /**
     * @Route("/avatar", name="avatar")
     */
    public function index()
    {
        $avatars = $this->getDoctrine()->getRepository(Avatar::class)->findAll();
        return $this->render('avatar/index.html.twig', [
            'controller_name' => 'AvatarController',
            'avatars' => $avatars
        ]);
    }
}
