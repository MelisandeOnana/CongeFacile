<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



class PreferencesType extends AbstractType
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $roles = $user->getRoles();

        if ($roles == "ROLE_MANAGER") {
            $builder
            ->add('alert_on_answer', EntityType::class, [
                'class' => 'App\Entity\Person',
                'choice_label' => 'alertOnAnswer',
                'label' => 'Être alerté par email lorsqu’une demande de arrive',
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
                'attr' => [
                    'class' => 'mt-1 block w-[350px] h-[46px] px-3 py-2 rounded-[6px] bg-[#F3F4F6]',
                ],
            ]);
        }
        

    }

}