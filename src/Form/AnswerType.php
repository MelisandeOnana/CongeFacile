<?php

namespace App\Form;

use App\Entity\Request;
use App\Entity\RequestType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use DateTime;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('answerComment', TextareaType::class, [
            'required' => false,
            'label' => 'Saisir un commentaire',
            'label_attr' => ['class' => 'text-[16px] mt-5 font-[Epilogue]'],
            'attr' => [
                'class' => 'border-[#DFE4EA] mt-2 w-[730px] h-[111px] border-2 rounded-[6px] px-5 py-4',
                'placeholder' => ''
            ],
            ])
            ->add('approve', SubmitType::class, [
                'label' => 'Validé',
                'attr' => [
                    'class' => 'btn btn-success',
                    'value' => 1
                ],
            ])
            ->add('reject', SubmitType::class, [
                'label' => 'Refusé',
                'attr' => [
                    'class' => 'btn btn-danger',
                    'value' => 2
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}