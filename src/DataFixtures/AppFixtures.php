<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\School;
use App\Entity\Comment;

use App\Entity\Classroom;
use App\Entity\Candidature;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Crée 1 administrateur
        $user = new User();

        $user->setUsername($faker->name);
        $user->setRoles(array("ROLE_ADMIN"));
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'admin'
        ));

        $manager->persist($user);

        $schoolNames = [
            "Jules Ferry",
            "Paul Bert", 
            "Saint-Éxupéry"
        ];

        $schoolAddresses = [
            "20 rue Jules Ferry 86000 Poitiers",
            "1 rue du Moulin à Vent 86000 Poitiers",
            "13 rue Évariste Galois 86000 Poitiers"
        ];

        $schoolEmails = [
            "jules.ferry@ac-poitiers.fr",
            "paul.bert@ac-poitiers.fr",
            "saint.exupery@ac-poitiers.fr"
        ];

        // Crée 3 écoles
        for($i = 0; $i < 3; $i++) {
            $school = new School();
            $school->setName($schoolNames[$i])
                   ->setAddress($schoolAddresses[$i])
                   ->setEmail($schoolEmails[$i]);

            $manager->persist($school);
        }

        // Crée 1 prof
        $teacher = new User();
        $teacher->setUsername('Nicolas Moinet');
        $teacher->setRoles(array("ROLE_TEACHER"));
        $teacher->setPassword($this->passwordEncoder->encodePassword(
            $teacher,
            'prof'
        ));

        $manager->persist($teacher);

        // Crée 1 classe
        $classroom = new Classroom();
        $classroom->setName('CM2');
        $classroom->setUser($teacher);
        $classroom->setYear(2020);
        $classroom->setSchool($school);

        $manager->persist($classroom);

        // Crée 1 candidature
        $candidature = new Candidature();
        $candidature->setClassroom($classroom);
        $candidature->setFirstname('Marcel');
        $candidature->setLastname('Proust');
        $candidature->setImageFilename('proust-5f76d8c168da8.jpeg');
        $candidature->setContent('Test');

        $date = new \DateTime();
        $candidature->setCreatedAt($date);
        $candidature->setIsValid(true);

        $manager->persist($candidature);

        // Crée 24 commentaires
        for($i = 0; $i < 24; $i++) {
            $comment = new Comment();
            $comment->setCandidature($candidature);
            $comment->setAuthor($faker->firstName);
            $comment->setContent($faker->sentence);
            $comment->setCreatedAt($faker->dateTimeBetween('-1 month'));

            $manager->persist($comment);
        }
        $manager->flush();
    }
}