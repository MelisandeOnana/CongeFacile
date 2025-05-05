<?php

namespace App\Form;

use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreferencesType extends AbstractType
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
            $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->authorizationChecker->isGranted('ROLE_MANAGER')) {
            $builder
            ->add('alertNewRequest', CheckboxType::class, [
                'label' => 'Être alerté par email lorsqu’une demande arrive',
                'required' => false,
                'label_attr' => ['class' => 'mt-3 ml-4 block font-medium text-gray-700'],
                'attr' => [
                    'class' => 'hidden peer', // Cache la case à cocher
                ],
            ]);
        } else {
            $builder
            ->add('alertOnAnswer', CheckboxType::class, [
                'label' => 'Être alerté par email lorsqu’une demande de congé est acceptée ou refusée',
                'required' => false,
                'label_attr' => ['class' => 'mt-3 ml-4 block font-medium text-gray-700'],
                'attr' => [
                    'class' => 'hidden peer', // Cache la case à cocher
                ],
            ])
            ->add('alertBeforeVacation', CheckboxType::class, [
                'label' => 'Recevoir un rappel par email lorsqu’un congé arrive la semaine prochaine',
                'required' => false,
                'label_attr' => ['class' => 'mt-3 ml-4 block font-medium text-gray-700'],
                'attr' => [
                    'class' => 'hidden peer', // Cache la case à cocher
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
