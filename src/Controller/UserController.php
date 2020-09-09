<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Form\LoginType;

class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function home()
    {
        return $this->render('user/home.html.twig');
    }

    /**
     * @Route("/about", name="app_about")
     */
    public function about()
    {
        return $this->render('user/about.html.twig');
    }

    /**
     * @Route("/calendar", name="app_calendar")
     */
    public function calendar()
    {
        return $this->render('user/calendar.html.twig');
    }

    /**
     * @Route("/candidatures", name="app_candidatures")
     */
    public function index()
    {
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route("/create/candidature", name="app_create_candidature")
     */
    public function create()
    {
        return $this->render('user/create.html.twig');
    }
}
