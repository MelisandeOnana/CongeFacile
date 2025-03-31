<?php

namespace App\Form;

use App\Entity\Request;
use App\Entity\RequestType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            'attr' => ['class' => 'appearance-none w-[350px] h-[46px] border rounded-[6px] pl-4 pr-4 text-[#9CA3AF]'],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]'],
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
        ])
        ->add('file', FileType::class, [
            'label' => 'Justificatif si applicable',
            'attr' => [
                'class' => 'w-[350px] h-[46px] border rounded-[6px]',
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
        ])
        ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $startAt = $data->getStartAt();
            $endAt = $data->getEndAt();
            $type = $data->getRequestType();

            if ($startAt >= $endAt) {
                $form->get('startAt')->addError(new FormError('La date de début doit être antérieure à la date de fin.'));
            }

            $today = new \DateTime('now');

            if ($startAt < $today) {
                $form->get('startAt')->addError(new FormError("La date et l'heure de début doit être postérieure à aujourd'hui"));
            }

            if (null == $type) {
                $form->get('requestType')->addError(new FormError('Le type de demande est obligatoire'));
            }
            if (null == $startAt) {
                $form->get('startAt')->addError(new FormError('La date de début est obligatoire'));
            }
            if (null == $endAt) {
                $form->get('endAt')->addError(new FormError('La date de fin est obligatoire'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}
