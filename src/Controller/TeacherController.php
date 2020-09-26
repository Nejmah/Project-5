<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Classroom;
use App\Entity\Candidature;
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

        // $users = $this->getUser()->getClassroom()->getUsers();
        // foreach($users as $user) {
        //     $user->getUsername();
        // }

        return $this->render('teacher/dashboard.html.twig', [
            'classroom' => $classroom
        ]);
    }

    /**
     * @Route("/teacher/candidatures", name="app_teacher_candidatures")
     */
    public function candidatures()
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $classroom = $this->getUser()->getClassroom();
        $candidatures = $this->getUser()->getClassroom()->getCandidatures();

        return $this->render('teacher/candidatures.html.twig', [
            'classroom' => $classroom,
            'candidatures' => $candidatures
        ]);
    }

    /**
     * @Route("/teacher/candidatures/validate/{id}", name="app_validate_candidature")
     */
    public function validate($id, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Candidature::class);
        $candidature = $repo->find($id);

        $candidatureFirstname = $candidature->getFirstname();
        $candidatureLastname = $candidature->getLastname();

        $candidature->setIsValid(true);
        $manager->persist($candidature);
        $manager->flush();

        $this->addFlash(
            'validate-candidature',
            "La candidature de $candidatureFirstname $candidatureLastname a été validée."
        );

        return $this->redirectToRoute('app_teacher');
    }

    /**
     * @Route("/teacher/candidatures/delete/{id}", name="app_delete_candidature")
     */
    public function delete($id, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Candidature::class);
        $candidature = $repo->find($id);

        $candidatureFirstname = $candidature->getFirstname();
        $candidatureLastname = $candidature->getLastname();

        $manager->remove($candidature);
        $manager->flush();

        $this->addFlash(
            'delete-candidature',
            "La candidature de $candidatureFirstname $candidatureLastname a été supprimé(e)."
        );

        return $this->redirectToRoute('app_teacher');
    }
}