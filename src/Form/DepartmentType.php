<?php

namespace App\Form;

use App\Entity\Department;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du département',
                'label_attr' => ['class' => 'block text-[16px] font-medium font-[Inter]'],
                'attr' => [
                    'class' => 'w-[350px] h-[46px] border rounded-[6px] pl-6 pr-6 mt-4',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du département est requis.']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom du département ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Department::class,
        ]);
    }
}