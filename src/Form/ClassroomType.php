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

class ClassroomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $schoolId = $options['schoolId'];
        $builder
            ->add('name', ChoiceType::class, [
                'choices' => [
                    'CE2' => 'CE2',
                    'CM1' => 'CM1',
                    'CM2' => 'CM2',
                ],
                'placeholder' => 'Choisissez une classe'
            ])
            ->add('teacher', EntityType::class, [
                'query_builder' => static function (EntityRepository $er) use ($schoolId) {
                    return $er->createQueryBuilder('teacher')
                        ->where('teacher.school = :schoolId')
                        ->setParameter('schoolId', $schoolId)
                    ;
                },
                'class' => User::class,
                'choice_label' => 'username',
                'placeholder' => 'Choisissez un professeur'
            ])
            ->add('year')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Classroom::class,
            'schoolId' => ""
        ]);

        $resolver->setAllowedTypes('schoolId', 'string');
    }
}
