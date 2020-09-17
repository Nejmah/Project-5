<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/teacher/new/user", name="add_user")
     */
    public function createUser()
    {
        return $this->render('teacher/addUser.html.twig');
    }

    /**
     * @Route("/teacher/delete/user/{id}", name="delete_user")
     */
    public function deleteUser($id, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);

        $userName = $user->getUsername();
        $userClassroom = $user->getClassroom()->getName();

        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'delete-user',
            "$userName a été supprimé(e) de la classe de $userClassroom."
        );

        return $this->redirectToRoute('app_teacher');
    }

}
