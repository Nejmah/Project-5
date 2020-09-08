<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Création de 3 utilisateurs, avec des rôles différents
        for($i = 1; $i <= 3; $i++) {
            $user = new User();
            $user->setUsername($faker->name);
            if ($i == 1) {
                $user->setRoles(array("ROLE_ADMIN"));
            }
            elseif ($i == 2) {
                $user->setRoles(array("ROLE_TEACHER"));
            }
            
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'the_new_password'
            ));
            
            $manager->persist($user);
        }
        $manager->flush();
    }
}
