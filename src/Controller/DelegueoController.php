<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DelegueoController extends AbstractController
{
    /**
     * @Route("/delegueo", name="delegueo")
     */
    public function index()
    {
        return $this->render('delegueo/index.html.twig', [
            'controller_name' => 'DelegueoController',
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('delegueo/home.html.twig');
    }
}
