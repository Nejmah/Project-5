<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\School;
use App\Entity\Comment;
use App\Entity\Classroom;
use App\Form\CommentType;

use App\Entity\Candidature;
use App\Form\CandidatureType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CandidatureController extends AbstractController
{
    /**
     * @Route("/candidature")
     */
    public function dashboard()
    {
        $repo = $this->getDoctrine()->getRepository(School::class);
        $schools = $repo->findBy([], ['id' => 'DESC']);

        return $this->render('candidature/dashboard.html.twig', [
            'schools' => $schools
        ]);
    }

    /**
     * @Route("/candidature/{classroomId}")
     */
    public function index($classroomId)
    {
        $repo = $this->getDoctrine()->getRepository(Classroom::class);
        $classroom = $repo->find($classroomId);

        $repo = $this->getDoctrine()->getRepository(Candidature::class);
        $candidatures = $repo->findBy([
            'classroom' => $classroom
        ]);

        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatures,
            'classroom' => $classroom
        ]);
    }

    /**
     * @Route("/candidature/show/{id}")
     */
    public function show($id, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();

        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Candidature::class);
        $candidature = $repo->find($id);
        $comments = $candidature->getComments();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setCandidature($candidature);
            $date = new \DateTime();
            $comment->setCreatedAt($date);

            $manager->persist($comment);
            $manager->flush();

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_candidature_show', [
                'id' => $id
            ]);
        }

        return $this->render('candidature/show.html.twig', [
            'formComment' => $form->createView(),
            'candidature' => $candidature,
            'comments' => $comments,
            'user' => $user
        ]);
    }

    /**
     * @Route("/candidature/create/{classroomId}")
     */
    public function create($classroomId, Request $request, EntityManagerInterface $manager)
    {
        $candidature = new Candidature();

        $repo = $this->getDoctrine()->getRepository(Classroom::class);
        $classroom = $repo->find($classroomId);
        
        $user = $this->getUser();

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

            $candidature->setClassroom($classroom);
            $date = new \DateTime();
            $candidature->setCreatedAt($date);

            if (!empty($user) && $user->isTeacher()) {
                $candidature->setIsValid(true);
            }
            else {
                $candidature->setIsValid(false);
            }

            $manager->persist($candidature);
            $manager->flush();

            $this->addFlash(
                'add-candidature',
                'Félicitations, ta candidature a bien été enregistrée !'
            );

            // Redirection vers l'espace administration
            return $this->redirectToRoute('app_user_home');
        }

        return $this->render('candidature/create.html.twig', [
            'formCandidature' => $form->createView()
        ]);
    }

    /**
     * @Route("/candidature/validate/{id}")
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
     * @Route("candidature/edit/{id}")
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
            return $this->redirectToRoute('app_user_home');
        }

        return $this->render('candidature/create.html.twig', [
            'formCandidature' => $form->createView()
        ]);
    }

    /**
     * @Route("candidature/delete/{id}")
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
}
