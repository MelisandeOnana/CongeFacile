<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamMemberSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', SearchType::class, [
                'label' => 'Nom',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher par nom...',
                    'class' => 'w-[126px] h-[44px] border-gray-300 rounded px-4 py-2 text-sm',
                ],
            ])
            ->add('firstName', SearchType::class, [
                'label' => 'Prénom',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher par prénom...',
                    'class' => 'w-[126px] h-[44px] border-gray-300 rounded px-4 py-2 text-sm',
                ],
            ])
            ->add('email', SearchType::class, [
                'label' => 'Email',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher par email...',
                    'class' => 'w-[200px] h-[44px] border-gray-300 rounded px-4 py-2 text-sm',
                ],
            ])
            ->add('position', SearchType::class, [
                'label' => 'Poste',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher par poste...',
                    'class' => 'w-[189px] h-[44px] border-gray-300 rounded px-4 py-2 text-sm',
                ],
            ])
            ->add('totalVacationDays', SearchType::class, [
                'label' => 'Nb congés posés sur l’année',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher par nb congés...',
                    'class' => 'w-[240px] h-[44px] border-gray-300 rounded px-4 py-2 text-sm',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}