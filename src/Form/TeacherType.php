<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Classroom;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class TeacherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $schoolId = $options['schoolId'];
        $builder
            ->add('username')
            ->add('classroom', EntityType::class, [
                'query_builder' => static function (EntityRepository $er) use ($schoolId) {
                    return $er->createQueryBuilder('classroom')
                        ->where('classroom.school = :schoolId')
                        ->setParameter('schoolId', $schoolId)
                    ;
                },
                // 'label' => 'Classe',
                'class' => Classroom::class,
                'choice_label' => 'name'
            ])
            ->add('password', PasswordType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'schoolId' => "1"
        ]);

        $resolver->setAllowedTypes('schoolId', 'string');
    }
}
