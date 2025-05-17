<?php

namespace App\Form;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoricRequestSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {  
        $builder->add('collaborator', ChoiceType::class, [
            'label' => 'Collaborateur',
            'choices' => array_combine(
                array_map(fn($person) => $person->getFirstName() . ' ' . $person->getLastName(), $options['collaborators']),
                array_map(fn($person) => $person->getId(), $options['collaborators'])
            ),
            'required' => false,
            'placeholder' => 'Sélectionnez un collaborateur',
            'attr' => [
            'class' => 'appearance-none text-sm font-medium mb-5 w-full h-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
            'onchange' => "updateUrl('collaborator', this.value)",

            ],
        ])
        ->add('requested', DateType::class, [
            'label' => 'Date de création',
            'required' => false,
            'attr' => [
            'type' => 'date',
            'class' => 'appearance-none text-sm font-medium mb-5 w-full h-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
            'onchange' => "updateUrl('requested', this.value)",
    
            ],
        ])
        ->add('start', DateType::class, [
            'label' => 'Date de début',
            'required' => false,
            'attr' => [
                'type' => 'date',
                'class' => 'appearance-none text-sm font-medium mb-5 w-full h-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
                'onchange' => "updateUrl('start', this.value)",
                
            ],
        ])
        ->add('end', DateType::class, [
            'label' => 'Date de fin',
            'required' => false,
            'attr' => [
                'type' => 'date',
                'class' => 'appearance-none text-sm font-medium mb-5 w-full h-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
                'onchange' => "updateUrl('end', this.value)",

            ],
        ])
        ->add('status', ChoiceType::class, [
            'label' => 'Statut',
            'choices' => [
                'En cours' => '3',
                'Accepté' => '1',
                'Refusé' => '2',
            ],
            'required' => false,
            'placeholder' => 'Sélectionnez un statut',
            'attr' => [
                'class' => 'appearance-none text-sm font-medium mb-5 w-full h-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
                'onchange' => "updateUrl('status', this.value)",
            ],
        ])
        ->add('type', ChoiceType::class, [
            'label' => 'Type de demande',
            'choices' => array_combine(
                array_map(fn($type) => $type->getName(), $options['types']),
                array_map(fn($type) => $type->getId(), $options['types'])
            ),
            'required' => false,
            'placeholder' => 'Sélectionnez un type de demande',
            'attr' => [
                'class' => 'appearance-none text-sm font-medium mb-5 w-full h-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
                'onchange' => "updateUrl('type', this.value)",
            ],
        ])
        ->add('days', NumberType::class, [
            'label' => 'Nb jours',
            'required' => false,
            'scale' => 1,
            'attr' => [
                'step' => '0.1',
                'class' => 'appearance-none text-sm font-medium mb-5 w-full h-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200 px-2',
                'onchange' => "updateUrl('days', this.value)",
            
            ],
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
            'collaborators' => [],
            'types' => [],
        ]);
    }
}