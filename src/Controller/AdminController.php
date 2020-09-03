<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Role;
use App\Entity\School;
use App\Form\RoleType;
use App\Form\SchoolType;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/new/role", name="add_roles")
     */
    public function createRole(Request $request)
    {
        $role = new Role();

        $form = $this->createForm(RoleType::class, $role);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($role);
            $manager->flush();

            // Redirection vers l'espace administration
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/roles.html.twig', [
            'formRole' => $form->createView()
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
     * @Route("/new/school", name="add_schools")
     */
    public function createSchool(Request $request)
    {
        $school = new School();

        $form = $this->createForm(SchoolType::class, $school);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
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
     * @Route("/new/classroom", name="add_classrooms")
     */
    public function createClassroom()
    {
        return $this->render('admin/classrooms.html.twig');
    }
}
