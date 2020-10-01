<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\School;
use App\Entity\Classroom;

use App\Form\TeacherType;
use App\Form\SchoolType;
use App\Form\ClassroomType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin")
     */
    public function dashboard()
    {
        $repo = $this->getDoctrine()->getRepository(School::class);
        $schools = $repo->findBy([], ['id' => 'DESC']);

        return $this->render('admin/dashboard.html.twig', [
            'schools' => $schools
        ]);
    }

    /**
     * @Route("/admin/new/school")
     */
    public function createSchool(Request $request, EntityManagerInterface $manager)
    {
        $school = new School();

        $form = $this->createForm(SchoolType::class, $school);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($school);
            $manager->flush();

            $this->addFlash(
                'add-school',
                'L\'école ' . $school->getName() . ' a été ajoutée avec succès !'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/addSchool.html.twig', [
            'formSchool' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/delete/school/{id}")
     */
    public function deleteSchool($id, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(School::class);
        $school = $repo->find($id);

        $manager->remove($school);
        $manager->flush();

        $this->addFlash(
            'delete-school',
            'L\'école ' . $school->getName() . ' a été supprimée.'
        );

        return $this->redirectToRoute('app_admin_dashboard');
    }

    /**
     * @Route("/admin/new/teacher/{schoolId}")
     */
    public function createTeacher($schoolId, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $user->setRoles(['ROLE_TEACHER']);

        $form = $this->createForm(TeacherType::class, $user);
        $form->handleRequest($request);

        $repo = $this->getDoctrine()->getRepository(School::class);
        $school = $repo->find($schoolId);

        if($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $password));

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'add-teacher',
                $user->getUsername() . ' a bien été ajouté(e) dans l\'école ' . $school->getname() . '.'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/addTeacher.html.twig', [
            'formTeacher' => $form->createView(),
            'school' => $school
        ]);
    }

    /**
     * @Route("/admin/new/classroom/{schoolId}")
     */
    public function createClassroom($schoolId, Request $request, EntityManagerInterface $manager)
    {
        $classroom = new Classroom();

        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);

        $repo = $this->getDoctrine()->getRepository(School::class);
        $school = $repo->find($schoolId);

        if($form->isSubmitted() && $form->isValid()) {
            $classroom->setSchool($school);
            $manager->persist($classroom);
            $manager->flush();

            $this->addFlash(
                'add-classroom',
                'La classe ' . $classroom->getName() . 
                ' a été ajoutée dans l\'école ' . $school->getname() . '.'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/addClassroom.html.twig', [
            'formClassroom' => $form->createView(),
            'school' => $school
        ]);
    }

    /**
     * @Route("/admin/delete/classroom/{id}")
     */
    public function deleteClassroom($id, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Classroom::class);
        $classroom = $repo->find($id);

        $manager->remove($classroom);
        $manager->flush();

        $this->addFlash(
            'delete-classroom',
            'La classe de ' . $classroom->getName() . ' a été supprimée de l\'école ' . $classroom->getSchool()->getName() . '.'
        );

        return $this->redirectToRoute('app_admin_dashboard');
    }
}