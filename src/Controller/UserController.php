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
     * @Route("/", name="home")
     */
    public function home(Request $request)
    {
        $user = new User();

        $form = $this->createForm(LoginType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // $manager = $this->getDoctrine()->getManager();
            // $manager->persist($user);
            // $manager->flush();

            // Accès au site après identification
            // return $this->render('delegueo/login.html.twig');
        }

        return $this->render('user/home.html.twig', [
            'formLogin' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        return $this->render('user/login.html.twig');
    }

    /**
     * @Route("/role", name="delegate_role")
     */
    public function roleDelegate()
    {
        return $this->render('user/delegate.html.twig');
    }

    /**
     * @Route("/calendar", name="calendar")
     */
    public function calendar()
    {
        return $this->render('user/calendar.html.twig');
    }

    /**
     * @Route("/candidatures", name="candidatures")
     */
    public function index()
    {
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route("/create/candidature", name="create_candidature")
     */
    public function create()
    {
        return $this->render('user/create.html.twig');
    }
}
