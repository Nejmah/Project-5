<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\School;
use DateTimeInterface;
use App\Form\LoginType;

use App\Entity\Classroom;
use App\Entity\Candidature;
use App\Form\CandidatureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function home()
    {
        return $this->render('user/home.html.twig');
    }

    /**
     * @Route("/about", name="app_about")
     */
    public function about()
    {
        return $this->render('user/about.html.twig');
    }

    /**
     * @Route("/calendar", name="app_calendar")
     */
    public function calendar()
    {
        return $this->render('user/calendar.html.twig');
    }

    /**
     * @Route("/candidatures", name="app_candidatures_index")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(School::class);
        $schools = $repo->findBy([], ['id' => 'DESC']);

        return $this->render('user/index.html.twig', [
            'schools' => $schools
        ]);
    }

    /**
     * @Route("/candidatures/list/{classroomId}", name="app_candidatures_list")
     */
    public function list($classroomId)
    {
        $repo = $this->getDoctrine()->getRepository(Classroom::class);
        $classroom = $repo->find($classroomId);

        $repo = $this->getDoctrine()->getRepository(Candidature::class);
        $candidatures = $repo->findBy([
            'classroom' => $classroom
        ]);

        return $this->render('user/candidatures.html.twig', [
            'candidatures' => $candidatures,
            'classroom' => $classroom
        ]);
    }

    /**
     * @Route("/candidature/create/{classroomId}", name="app_create_candidature")
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
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/create.html.twig', [
            'formCandidature' => $form->createView()
        ]);
    }

    /**
     * @Route("/candidature/{id}", name="app_candidature")
     */
    public function show($id)
    {
        $repo = $this->getDoctrine()->getRepository(Candidature::class);
        $candidature = $repo->find($id);
        $classroom = $candidature->getClassroom();

        return $this->render('user/candidature.html.twig', [
            'candidature' => $candidature,
            'classroom' => $classroom
        ]);
    }
}
