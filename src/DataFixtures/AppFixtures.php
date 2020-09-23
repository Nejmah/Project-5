<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Entity\School;
use App\Entity\Classroom;

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
        $manager->flush();

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

            // //Crée entre 2 et 4 profs
            // for($j = 0; $j < mt_rand(2, 4); $j++) {
            //     $user = new User();

            //     $user->setRoles(array("ROLE_TEACHER"));
            //     $user->setUsername($faker->name);
            //     $user->setPassword($this->passwordEncoder->encodePassword(
            //         $user,
            //         'prof'
            //     ));

            //     $manager->persist($user);
            // }
        }
        $manager->flush();
    }
}


            // // Crée 2 classes
            // for($j = 0; $j < 2; $j++) {
            //     $classroom = new Classroom();

            //     if ($j == 0) {
            //         $classroom->setName('CM1');
            //     }
            //     elseif ($j == 1) {
            //         $classroom->setName('CM2');
            //     }
            //     $classroom->setYear(2020);
            //     $classroom->setSchool($school);
                        
            //     $manager->persist($classroom);

            //     $user = new User();

            //     $user->setRoles(array("ROLE_TEACHER"));
            //     $user->setUsername($faker->name);
            //     $user->setPassword($this->passwordEncoder->encodePassword(
            //         $user,
            //         'prof'
            //     ));
            //     $user->setClassroom($classroom);

            //     $manager->persist($user);

            //     // Crée 1 prof par classe
            //         for($k = 0; $k < 1; $k++) {
            //         $user = new User();
            //         $user->setUsername($faker->name);
            //         $user->setPassword($this->passwordEncoder->encodePassword(
            //             $user,
            //             'élève'
            //         ));
            //         $user->setClassroom($classroom);

            //         $manager->persist($user);    
            //         }
            //     }
