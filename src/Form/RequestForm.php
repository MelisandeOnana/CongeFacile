<?php

namespace App\Form;

use App\Entity\Request;
use App\Entity\RequestType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use DateTime;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
            'attr' => ['class' => 'appearance-none w-[350px] h-[46px] border rounded-[6px] pl-4 pr-4 text-[#9CA3AF]'],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]']  // Ajout de la classe CSS pour le label
        ])
        ->add('startAt', DateTimeType::class, [
            'label' => 'Date de début - champ obligatoire',
            'widget' => 'single_text',
            'attr' => [
            'id' => 'startDate',
            'onchange' => 'calculateBusinessDays()',
            'class' => 'w-[350px] h-[46px] border rounded-[6px] pl-6 pr-6'],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]']
        ])
        ->add('endAt', DateTimeType::class, [
            'label' => 'Date de fin - champ obligatoire',
            'widget' => 'single_text',
            'attr' => [
                'id' => 'endDate',
                'onchange' => 'calculateBusinessDays()',
                'class' => 'w-[350px] h-[46px] border rounded-[6px] pl-6 pr-6'],
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]']
        ])
        ->add('fichier', FileType::class, [
            'label' => 'Justificatif si applicable',
            'attr' => [
                'class' => 'w-[350px] h-[46px] border rounded-[6px]',
            ],
            'mapped' => false,
            'required' => false,
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter] ']
        ])
        ->add('comment', null, [
            'label' => 'Commentaire supplémentaire',
            'attr' => ['class' => 'w-[730px] h-[186px] border rounded-[6px] p-4',
                    'placeholder' => 'Si congé exceptionnel ou sans solde, vous pouvez préciser votre demande.'],
            'required' => false,
            'label_attr' => ['class' => 'block mb-2 text-[#212B36] font-[Inter]'],
            'empty_data' => ''
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

            $today = new DateTime('now');

            if ($startAt < $today) {
                $form->get('startAt')->addError(new FormError("La date et l'heure de début doit être postérieure à aujourd'hui"));
            }

            if ($type == null ) {
                $form->get('requestType')->addError(new FormError("Le type de demande est obligatoire"));
            }
            if ($startAt == null) {
                $form->get('startAt')->addError(new FormError("La date de début est obligatoire"));
            }
            if ($endAt == null) {
                $form->get('endAt')->addError(new FormError("La date de fin est obligatoire"));
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