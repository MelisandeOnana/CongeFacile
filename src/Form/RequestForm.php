<?php

namespace App\Form;

use App\Entity\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use App\Entity\RequestType;

class RequestForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('requestType', EntityType::class, [
            'class' => RequestType::class,               // L'entité étrangère
            'choice_label' => 'name',                  // Le champ à afficher dans la liste déroulante
            'placeholder' => 'Sélectionner un type',     // Texte de placeholder (optionnel)
            'label' => 'Type de demande - champ obligatoire',                       // Libellé du champ
            'required' => true,                        // Rendre le champ obligatoire
            'attr' => ['class' => 'w-[350px] h-[46px] border rounded-[6px]'],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]']  // Ajout de la classe CSS pour le label
        ])
        ->add('startAt', DateTimeType::class, [
            'label' => 'Date début - champ obligatoire',
            'widget' => 'single_text',
            'attr' => ['class' => 'w-[350px] h-[46px] border rounded-[6px]'],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]']
        ])
        ->add('endAt', DateTimeType::class, [
            'label' => 'Date de fin - champ obligatoire',
            'widget' => 'single_text',
            'attr' => ['class' => 'w-[350px] h-[46px] border rounded-[6px]'],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]']
        ])
        ->add('fichier', FileType::class, [
            'label' => 'Justificatif si applicable',
            'attr' => [
                'class' => 'w-[350px] h-[46px] border rounded-[6px]',
                'placeholder' => 'Sélectionner un fichier'
            ],
            'mapped' => false,
            'required' => false,
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter] ']
        ])
        ->add('comment', null, [
            'label' => 'Commentaire supplémentaire',
            'attr' => ['class' => 'w-[730px] h-[186px] border rounded-[6px]',
                    'placeholder' => 'Si congé exceptionnel ou sans solde, vous pouvez préciser votre demande.'],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]']
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}