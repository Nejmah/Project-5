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
        $user = $repo->findOneByUsername('Claudine Carlier');
        $users = $user->getClassroom()->getUsers();
        foreach($users as $user) {
            dump($user->getUsername());
        }
        die;

        return $this->render('teacher/dashboard.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/new/student", name="add_student")
     */
    public function createUser()
    {
        return $this->render('teacher/addUser.html.twig');
    }

}
