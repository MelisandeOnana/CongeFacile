<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email - champ obligatoire',
                'required' => true,
                'label_attr' => ['class' => 'mb-[10px] block text-sm font-medium text-[#111928]'],
                'attr' => [
                    'class' => 'mb-[15px] block w-[350px] h-[46px] px-3 py-2 rounded-[6px] border-[1px] border-[#E5E7EB] pl-10',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez saisir une adresse email.']),
                    new Assert\Email(['message' => 'L\'adresse email "{{ value }}" n\'est pas une adresse valide.']),
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
                'placeholder' => 'Choisir un département',
                'attr' => [
                    'id' => 'department',
                    'class' => 'appearance-none mb-[15px] block w-[350px] h-[46px] px-3 py-2 rounded-[6px] border-[1px] border-[#E5E7EB]',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez sélectionner un département.']),
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
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez saisir un mot de passe.']),
                    new Assert\Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins 8 caractères.',
                    ]),
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
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez confirmer le mot de passe.']),
                ],
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            // Vérifiez si les mots de passe correspondent
            if ($form->get('newPassword')->getData() !== $form->get('confirmPassword')->getData()) {
                $form->get('confirmPassword')->addError(new FormError('Les mots de passe ne correspondent pas.'));
            }

            // Assurez-vous que le département est défini
            $department = $form->get('department')->getData();
            if (!$department) {
                $form->get('department')->addError(new FormError('Le département ne peut pas être vide.'));
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
