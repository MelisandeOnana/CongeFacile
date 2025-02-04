<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Person;
use App\Entity\Department;
use App\Entity\Position;
use Doctrine\ORM\EntityRepository;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $department = $options['department'];
        $isManager = $options['is_manager'];

        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Nom de famille',
                'label_attr' => ['class' => 'block text-sm font-medium text-[#6B7280] font-[Inter]'],
                'attr' => [
                    'class' => 'mt-1 block w-[350px] h-[46px] px-3 py-2 rounded-[6px] bg-[#F3F4F6]',
                    'readonly' => true,
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'label_attr' => ['class' => 'block text-sm font-medium text-[#6B7280] font-[Inter]'],
                'attr' => [
                    'class' => 'mt-1 block w-[350px] h-[46px] px-3 py-2 rounded-[6px] bg-[#F3F4F6]',
                    'readonly' => true,
                ],
            ])
            ->add('email', EmailType::class, [
                'mapped' => false,
                'label' => 'Adresse mail',
                'label_attr' => ['class' => 'block text-sm font-medium text-[#6B7280] font-[Inter]'],
                'attr' => [
                    'class' => 'mt-1 block w-[350px] h-[46px] px-[2.75rem] pr-[0.75rem] py-2 rounded-[6px] bg-[#F3F4F6]',
                    'readonly' => true,
                ],
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'name',
                'label' => 'Direction/Service',
                'label_attr' => ['class' => 'block text-sm font-medium text-[#6B7280] font-[Inter]'],
                'attr' => [
                    'class' => 'appearance-none mt-1 block w-[350px] h-[46px] px-3 py-2 rounded-[6px] bg-[#F3F4F6]',
                    'disabled' => true,
                ],
            ]);

        if (!$isManager) {
            $builder
                ->add('position', EntityType::class, [
                    'class' => Position::class,
                    'choice_label' => 'name',
                    'label' => 'Poste',
                    'label_attr' => ['class' => 'block text-sm font-medium text-[#6B7280] font-[Inter]'],
                    'attr' => [
                        'class' => 'appearance-none mt-1 block w-[350px] h-[46px] px-3 py-2 rounded-[6px] bg-[#F3F4F6]',
                        'disabled' => true,
                    ],
                ])
                ->add('manager', EntityType::class, [
                    'class' => Person::class,
                    'query_builder' => function (EntityRepository $er) use ($department) {
                        return $er->createQueryBuilder('p')
                            ->where('p.department = :department')
                            ->andWhere('p.position = :managerPosition')
                            ->setParameter('department', $department)
                            ->setParameter('managerPosition', $er->getEntityManager()->getRepository(Position::class)->findOneBy(['name' => 'Manager']));
                    },
                    'choice_label' => function (Person $person) {
                        return $person->getFirstName() . ' ' . $person->getLastName();
                    },
                    'label' => 'Manager',
                    'label_attr' => ['class' => 'block text-sm font-medium text-[#6B7280] font-[Inter]'],
                    'attr' => [
                        'class' => 'appearance-none mt-1 block w-[350px] h-[46px] px-3 py-2 rounded-[6px] bg-[#F3F4F6]',
                        'disabled' => true,
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
            'department' => null,
            'is_manager' => false,
        ]);
    }
}