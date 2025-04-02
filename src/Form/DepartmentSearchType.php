<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('search', SearchType::class, [
            'label' => 'Nom de la direction ou du service',
            'required' => false,
            'label_attr' => [
                'class' => 'text-[14px] font-medium text-[#344054] normal-case' // Ajout de normal-case
            ],
            'attr' => [
                'placeholder' => 'Rechercher un département...',
                'class' => 'w-[558px] h-[44px] border rounded-[6px] pl-6 pr-6 mt-4'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
        ]);
    }
}
