<?php

namespace App\Form;

use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Nom de famille - champ obligatoire',
                'required' => true,
                'label_attr' => ['class' => 'mb-[10px] block text-sm font-medium text-[#111928]'],
                'attr' => [
                    'class' => 'mb-[15px] block w-[350px] h-[46px] px-3 py-2 rounded-[6px] border-[1px] border-[#E5E7EB]',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom de famille est obligatoire.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom de famille ne peut pas dépasser 255 caractères.',
                    ]),
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom - champ obligatoire',
                'required' => true,
                'label_attr' => ['class' => 'mb-[10px] block text-sm font-medium text-[#111928]'],
                'attr' => [
                    'class' => 'mb-[15px] block w-[350px] h-[46px] px-3 py-2 rounded-[6px] border-[1px] border-[#E5E7EB]',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le prénom ne peut pas dépasser 255 caractères.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
