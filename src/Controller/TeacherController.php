<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Classroom;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("/teacher/new/user/{classroomId}", name="add_user")
     */
    public function createUser($classroomId, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $repo = $this->getDoctrine()->getRepository(Classroom::class);
        $classroom = $repo->find($classroomId);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setClassroom($classroom);
            $user->setPassword($passwordEncoder->encodePassword($user, 'élève'));
            $manager->persist($user);
            $manager->flush();
        
            $this->addFlash(
                'add-user',
                'L\'élève ' . $user->getUsername() . ' a été ajouté(e).'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_teacher');
        }

        return $this->render('teacher/addUser.html.twig', [
            'formUser' => $form->createView()
        ]);
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