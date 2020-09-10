<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\School;
use App\Form\SchoolType;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function dashboard()
    {
        $repo = $this->getDoctrine()->getRepository(School::class);

        $schools = $repo->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'schools' => $schools
        ]);
    }

    /**
     * @Route("/new/school", name="add_schools")
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
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/schools.html.twig', [
            'formSchool' => $form->createView()
        ]);
    }

    /**
     * @Route("/new/user", name="add_users")
     */
    public function createUser()
    {
        return $this->render('admin/users.html.twig');
    }

    /**
     * @Route("/new/classroom", name="add_classrooms")
     */
    public function createClassroom()
    {
        return $this->render('admin/classrooms.html.twig');
    }
}
