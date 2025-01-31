<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
                'attr' => [
                    'class' => 'mt-1 block w-[350px] h-[46px] px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1B3168] focus:border-[#1B3168] sm:text-sm',
                    'data-toggle' => 'password',
                    'id' => 'resetPasswordForm_currentPassword',
                    'data-target' => 'resetPasswordForm_currentPassword',
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre mot de passe actuel',
                    ]),
                ],
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
                'attr' => [
                    'class' => 'mt-1 block w-[350px] h-[46px] px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1B3168] focus:border-[#1B3168] sm:text-sm',
                    'data-toggle' => 'password',
                    'id' => 'resetPasswordForm_newPassword',
                    'data-target' => 'resetPasswordForm_newPassword',
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nouveau mot de passe',
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmation de mot de passe',
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
                'attr' => [
                    'class' => 'mt-1 block w-[350px] h-[46px] px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1B3168] focus:border-[#1B3168] sm:text-sm',
                    'data-toggle' => 'password',
                    'id' => 'resetPasswordForm_confirmPassword',
                    'data-target' => 'resetPasswordForm_confirmPassword',
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez confirmer votre nouveau mot de passe',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}