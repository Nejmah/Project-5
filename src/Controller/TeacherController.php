<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;

use App\Form\UserPasswordType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TeacherController extends AbstractController
{
    /**
     * @Route("/teacher")
     */
    public function dashboard()
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $classroom = $this->getUser()->getClassroom();
        $candidatures = $classroom->getCandidatures();

        return $this->render('teacher/dashboard.html.twig', [
            'classroom' => $classroom,
            'candidatures' => $candidatures
        ]);
    }

    /**
     * @Route("/teacher/candidatures")
     */
    public function candidatures()
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $classroom = $this->getUser()->getClassroom();
        $candidatures = $classroom->getCandidatures();

        return $this->render('teacher/candidatures.html.twig', [
            'classroom' => $classroom,
            'candidatures' => $candidatures
        ]);
    }
    
    /**
     * @Route("/teacher/comment/delete/{candidatureId}/{commentId}")
     */
    public function deleteComment($candidatureId, $commentId, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Comment::class);
        $comment = $repo->find($commentId);

        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute('app_candidature_show', [
            'id' => $candidatureId
        ]);
    }

    /**
     * @Route("/teacher/password")
     */
    public function password(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $this->getUser();

        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $password));

            $manager->flush();

            $this->addFlash(
                'update-password',
                'Votre nouveau mot de passe a bien été enregistré.'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_teacher_dashboard');
        }

        return $this->render('teacher/password.html.twig', [
            'formPassword' => $form->createView()
        ]);
    }
}