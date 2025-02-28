<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Person;
use App\Entity\Department;
use App\Entity\Position;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;
use App\Validator\PasswordMatch;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email  - champ obligatoire',
                'required' => true,
                'label_attr' => ['class' => 'mb-[10px] block text-sm font-medium text-[#111928]'],
                'attr' => [
                    'class' => 'mb-[15px] block w-[350px] h-[46px] px-3 py-2 rounded-[6px] border-[1px] border-[#E5E7EB] pl-10',
                ], 
            ])
            ->add('person', PersonType::class, [
                'label' => false,
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'name',
                'label' => 'Direction service - champ obligatoire',
                'required' => true,
                'label_attr' => ['class' => 'mb-[10px] block text-sm font-medium text-[#111928]'],
                'mapped' => false,
                'placeholder' => 'Choisir un département', 
                'attr' => [
                    'id' => 'department',
                    'class' => 'mb-[15px] block w-[350px] h-[46px] px-3 py-2 rounded-[6px] border-[1px] border-[#E5E7EB]',
                ], 
            ])
            ->add('position', EntityType::class, [
                'class' => Position::class,
                'choice_label' => 'name',
                'label' => 'Poste - champ obligatoire',
                'required' => true,
                'label_attr' => ['class' => 'mb-[10px] block text-sm font-medium text-[#111928]'],
                'mapped' => false,
                'placeholder' => 'Choisir un poste', 
                'attr' => [
                    'class' => 'mb-[15px] block w-[350px] h-[46px] px-3 py-2 rounded-[6px] border-[1px] border-[#E5E7EB]',
                ], 
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'label_attr' => ['class' => 'mb-[10px] block text-sm font-medium text-[#111928]'],
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'mb-[15px] block w-[350px] h-[46px] px-3 py-2 rounded-[6px] border-[1px] border-[#E5E7EB]',
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmation de mot de passe',
                'label_attr' => ['class' => 'mb-[10px] block text-sm font-medium text-[#111928]'],
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'mb-[15px] block w-[350px] h-[46px] px-3 py-2 rounded-[6px] border-[1px] border-[#E5E7EB]',
                ],
            ]);

            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
            
                // Vérifiez si les mots de passe correspondent
                if ($form->get('newPassword')->getData() !== $form->get('confirmPassword')->getData()) {
                    $form->get('confirmPassword')->addError(new FormError('Les mots de passe ne correspondent pas.'));
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}