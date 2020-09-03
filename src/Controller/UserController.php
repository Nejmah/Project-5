<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Form\UserType;

class UserController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

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
     * @Route("/candidatures", name="candidatures")
     */
    public function index()
    {
        return $this->render('user/index.html.twig');
    }

}
