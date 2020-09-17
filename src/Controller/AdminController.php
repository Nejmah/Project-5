<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\School;
use App\Form\UserType;
use App\Form\SchoolType;
use App\Entity\Classroom;

use App\Form\TeacherType;
use App\Form\ClassroomType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
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
     * @Route("/admin/new/school", name="add_school")
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
                'school',
                'L\'école ' . $school->getName() . ' a été ajoutée avec succès !'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/addSchool.html.twig', [
            'formSchool' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/delete/school/{id}", name="delete_school")
     */
    public function deleteSchool($id, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(School::class);
        $school = $repo->find($id);

        $manager->remove($school);
        $manager->flush();

        return $this->redirectToRoute('app_admin');
    }

    /**
     * @Route("/admin/new/teacher/{schoolId}", name="add_teacher")
     */
    public function createTeacher($schoolId, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $user->setRoles(['ROLE_TEACHER']);

        $form = $this->createForm(TeacherType::class, $user, ['schoolId' => $schoolId]);
        $form->handleRequest($request);

        $repo = $this->getDoctrine()->getRepository(School::class);
        $school = $repo->find($schoolId);

        if($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $password));

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'teacher',
                $user->getUsername() . ' a bien été ajouté(e) dans l\'école ' . $school->getname() . '.'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/addTeacher.html.twig', [
            'formTeacher' => $form->createView(),
            'school' => $school
        ]);
    }

    /**
     * @Route("/admin/new/classroom/{school_id}", name="add_classroom")
     */
    public function createClassroom($school_id, Request $request, EntityManagerInterface $manager)
    {
        $classroom = new Classroom();

        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);

        $repo = $this->getDoctrine()->getRepository(School::class);
        $school = $repo->find($school_id);

        if($form->isSubmitted() && $form->isValid()) {
            $classroom->setSchool($school);
            $manager->persist($classroom);
            $manager->flush();

            $this->addFlash(
                'classroom',
                'La classe ' . $classroom->getName() . 
                ' a été ajoutée dans l\'école ' . $school->getname() . '.'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/addClassroom.html.twig', [
            'formClassroom' => $form->createView(),
            'school' => $school
        ]);
    }
}