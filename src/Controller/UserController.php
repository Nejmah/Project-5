<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\School;
use App\Form\LoginType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function schoolIndex()
    {
        $repo = $this->getDoctrine()->getRepository(School::class);
        $schools = $repo->findBy([], ['id' => 'DESC']);

        return $this->render('user/schools.html.twig', [
            'schools' => $schools
        ]);
    }

    /**
     * @Route("/create/candidature/{classroomId}", name="app_form_candidature")
     */
    public function create($classroomId)
    {
        return $this->render('user/create.html.twig');
    }
}
