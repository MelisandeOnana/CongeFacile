<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PositionSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', SearchType::class, [
            'label' => 'Nom du poste',
            'required' => false,
            'attr' => [
                'class' => 'appearance-none text-sm font-medium mb-5 w-[558px] h-[44px] border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
                'onchange' => "updateUrl('name', this.value)",

            ]
        ])
        ->add('number', SearchType::class, [
            'label' => 'Nb personnes liées',
            'required' => false,
            'attr' => [
                'class' => 'appearance-none text-sm font-medium mb-5 w-[150] h-[44px] border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
                'onchange' => "updateUrl('number', this.value)",
            ]
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