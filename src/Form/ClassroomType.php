<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Classroom;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ClassroomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', ChoiceType::class, [
                'choices' => [
                    'CE2' => 'CE2',
                    'CM1' => 'CM1',
                    'CM2' => 'CM2',
                ],
                'placeholder' => 'Choisissez une classe'
            ])
            ->add('user', EntityType::class, [
                'query_builder' => static function (EntityRepository $er) {
                    return $er->createQueryBuilder('user')
                        ->where('user.roles LIKE :role')
                        ->andWhere('user.classroom IS NULL')
                        ->setParameter('role', '%ROLE_TEACHER%')
                    ;
                },
                'class' => User::class,
                'choice_label' => 'username',
                'placeholder' => 'Choisissez un professeur'
            ])
            ->add('year', IntegerType::class, array(
                'attr' => array('min' => 2020, 'max' => 2021)
        ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Classroom::class,
        ]);
    }
}
