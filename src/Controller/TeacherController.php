<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Classroom;
use App\Form\PasswordType;
use App\Entity\Candidature;
use App\Form\CandidatureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
        $candidatures = $classroom->getCandidatures();

        // $users = $this->getUser()->getClassroom()->getUsers();
        // foreach($users as $user) {
        //     $user->getUsername();
        // }

        return $this->render('teacher/dashboard.html.twig', [
            'classroom' => $classroom,
            'candidatures' => $candidatures
        ]);
    }

    /**
     * @Route("/teacher/candidatures", name="app_teacher_candidatures")
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

        return $this->redirectToRoute('app_teacher_candidatures');
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

        return $this->redirectToRoute('app_teacher_candidatures');
    }

    /**
     * @Route("/teacher/candidatures/edit/{id}", name="app_edit_candidature")
     */
    public function edit($id, Request $request, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Candidature::class);
        $candidature = $repo->find($id);

        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $candidature->setImageFilename($newFilename);
            }

            $candidature->setIsValid(true);

            $manager->flush();

            $this->addFlash(
                'add-candidature',
                'Félicitations, ta candidature a bien été modifiée !'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/create.html.twig', [
            'formCandidature' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/teacher/password", name="app_change_password")
     */
    public function change(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $this->getUser();

        $form = $this->createForm(PasswordType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->addFlash(
                'update-password',
                'Votre nouveau mot de passe a bien été enregistré.'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_teacher');
        }

        return $this->render('teacher/password.html.twig', [
            'formPassword' => $form->createView()
        ]);
    }

    // /**
    //  * @Route("/teacher/comments", name="app_teacher_comments")
    //  */
    // public function comments()
    // {
    //     $repo = $this->getDoctrine()->getRepository(User::class);
    //     $classroom = $this->getUser()->getClassroom();
    //     $candidatures = $classroom->getCandidatures();

    //     return $this->render('teacher/comments.html.twig', [
    //         'classroom' => $classroom,
    //         'candidatures' => $candidatures
    //     ]);
    // }
}