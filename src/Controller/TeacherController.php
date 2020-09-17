<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeacherController extends AbstractController
{
    /**
     * @Route("/teacher", name="app_teacher")
     */
    public function dashboard()
    {
        $repo = $this->getDoctrine()->getRepository(User::class);

        $classroom = $this->getUser()->getClassroom();

        $users = $this->getUser()->getClassroom()->getUsers();
        foreach($users as $user) {
            $user->getUsername();
        }

        return $this->render('teacher/dashboard.html.twig', [
            'classroom' => $classroom,
            'users' => $users
        ]);
    }

    /**
     * @Route("/new/user", name="add_user")
     */
    public function createUser()
    {
        return $this->render('teacher/addUser.html.twig');
    }

}
