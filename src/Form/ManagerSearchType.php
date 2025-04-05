<?php

namespace App\Form;

use App\Entity\Department;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManagerSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('lastname', SearchType::class, [
            'label' => 'Nom de famille',
            'required' => false,
            'attr' => [
                'class' => 'appearance-none text-sm font-medium mb-5 w-[200px] h-[44px] border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
                'onchange' => "updateUrl('lastname', this.value)",

            ]
        ])
        ->add('firstname', SearchType::class, [
            'label' => 'Prénom',
            'required' => false,
            'attr' => [
                'class' => 'appearance-none text-sm font-medium mb-5 w-[200px] h-[44px] border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
                'onchange' => "updateUrl('firstname', this.value)",
            ]
        ])
        ->add('department', EntityType::class, [
            'class' => Department::class,
            'choice_label' => 'name',
            'label_attr' => ['class' => 'mb-[10px] block text-sm font-medium text-[#111928]'],
            'placeholder' => 'Choisir un département',
            'attr' => [
                'id' => 'department',
                'class' => 'appearance-none text-sm font-medium mb-5 w-[200px] h-[44px] border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
                'onchange' => "updateUrl('department', this.value)",
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