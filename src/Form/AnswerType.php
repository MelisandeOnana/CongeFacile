<?php

namespace App\Form;

use App\Entity\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('answerComment', TextareaType::class, [
                'required' => true,
                'label' => 'Saisir un commentaire',
                'label_attr' => ['class' => 'text-[16px] mt-5 font-[Epilogue]'],
                'attr' => [
                    'class' => 'border-[#DFE4EA] mt-2 w-[730px] h-[111px] border-2 rounded-[6px] px-5 py-4',
                    'placeholder' => '',
                    'value' => ''
                ],
            ])
            ->add('reject', SubmitType::class, [
                'label' => 'Refuser la demande',
                'attr' => [
                    'class' => 'btn flex items-center justify-center w-[209px] h-[40px] rounded-[6px] text-[16px] text-white bg-[#E10E0E]',
                ],
            ])
            ->add('approve', SubmitType::class, [
                'label' => 'Valider la demande',
                'attr' => [
                    'class' => 'btn flex items-center justify-center w-[209px] h-[40px] ml-5 rounded-[6px] text-[16px] text-white bg-[#1A8245]',
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