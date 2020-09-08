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

        // Création de 52 utilisateurs
        for($i = 1; $i <= 52; $i++) {
            $user = new User();
            $user->setUsername($faker->name);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'the_new_password'
            ));
            
            $manager->persist($user);
        }
        $manager->flush();
    }
}
