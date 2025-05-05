<?php

namespace App\Form;

use App\Entity\RequestType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class RequestForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('requestType', EntityType::class, [
            'class' => RequestType::class,
            'choice_label' => 'name',
            'placeholder' => 'Sélectionner un type',
            'label' => 'Type de demande - champ obligatoire',
            'required' => true,
            'attr' => ['class' => 'appearance-none w-[350px] h-[46px] border rounded-[6px] pl-4 pr-4'],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]'],
            'constraints' => [
                new Assert\NotNull(['message' => 'Le type de demande est obligatoire.']),
            ],
        ])
        ->add('startAt', DateTimeType::class, [
            'label' => 'Date de début - champ obligatoire',
            'widget' => 'single_text',
            'attr' => [
                'id' => 'startDate',
                'onchange' => 'calculateBusinessDays()',
                'class' => 'w-[350px] h-[46px] border rounded-[6px] pl-6 pr-6',
            ],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]'],
            'constraints' => [
                new Assert\NotNull(['message' => 'La date de début est obligatoire.']),
                new Assert\GreaterThanOrEqual([
                    'value' => 'today',
                    'message' => 'La date de début doit être postérieure ou égale à aujourd\'hui.',
                ]),
            ],
        ])
        ->add('endAt', DateTimeType::class, [
            'label' => 'Date de fin - champ obligatoire',
            'widget' => 'single_text',
            'attr' => [
                'id' => 'endDate',
                'onchange' => 'calculateBusinessDays()',
                'class' => 'w-[350px] h-[46px] border rounded-[6px] pl-6 pr-6',
            ],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]'],
            'constraints' => [
                new Assert\NotNull(['message' => 'La date de fin est obligatoire.']),
                new Assert\Expression([
                    'expression' => 'value > this.getParent().get("startAt").getData()',
                    'message' => 'La date de fin doit être postérieure à la date de début.',
                ]),
            ],
        ])
        ->add('file', FileType::class, [
            'label' => 'Justificatif si applicable',
            'attr' => [
                'class' => 'w-[350px] h-[46px] border rounded-[6px] opacity-0 absolute inset-0 cursor-pointer',
            ],
            'mapped' => false,
            'required' => false,
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter] '],
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'application/pdf',
                        'image/png',
                        'image/jpeg',
                        'image/gif',
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF ou une image valide (PNG, JPEG, GIF).',
                    'maxSize' => '5M',
                    'maxSizeMessage' => 'Le fichier ne doit pas dépasser 5 Mo.',
                ]),
            ],
        ])
        ->add('comment', null, [
            'label' => 'Commentaire supplémentaire',
            'attr' => [
                'class' => 'w-[730px] h-[186px] border rounded-[6px] p-4 sm:w-[655px]',
                'placeholder' => 'Si congé exceptionnel ou sans solde, vous pouvez préciser votre demande.',
            ],
            'required' => false,
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]'],
            'empty_data' => '',
            'constraints' => [
                new Assert\Length([
                    'max' => 500,
                    'maxMessage' => 'Le commentaire ne peut pas dépasser 500 caractères.',
                ]),
            ],
        ]);
    }
}
