<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('delete', SubmitType::class, [
                'label' => 'Supprimer',
                'attr' => [
                    'class' => 'btn flex items-center justify-center w-[124px] h-[40px] rounded-[6px] text-[16px] text-white bg-[#E10E0E]',
                ],
            ])
            ->add('_token', HiddenType::class, [
                'data' => 'formDelete',
            ]);
    }
}
