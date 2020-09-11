<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\School;
use App\Form\UserType;
use App\Form\SchoolType;

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

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/schools.html.twig', [
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
     * @Route("/admin/new/user", name="add_user")
     */
    public function createUser(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $user->setRoles(['ROLE_TEACHER']);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $password));

            $manager->persist($user);
            $manager->flush();

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/users.html.twig', [
            'formUser' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/new/classroom", name="add_classroom")
     */
    public function createClassroom()
    {
        return $this->render('admin/classrooms.html.twig');
    }
}
