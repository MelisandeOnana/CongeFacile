<?php

namespace App\Form;

use App\Entity\RequestType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RequestTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du type',
                'label_attr' => ['class' => 'block text-[16px] font-medium font-[Inter]'],
                'attr' => [
                    'class' => 'w-[350px] h-[46px] border rounded-[6px] pl-6 pr-6 mt-4',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RequestType::class,
        ]);
    }
}