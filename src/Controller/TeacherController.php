<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
{
    /**
     * @Route("/teacher", name="teacher")
     */
    public function index()
    {
        return $this->render('teacher/index.html.twig');
    }

    /**
     * @Route("/new/student", name="add_students")
     */
    public function createStudent()
    {
        return $this->render('teacher/students.html.twig');
    }

}
