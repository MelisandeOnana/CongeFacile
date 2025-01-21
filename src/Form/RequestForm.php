<?php

namespace App\Form;

use App\Entity\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\RequestType;

class RequestForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('requestType', EntityType::class, [
            'class' => RequestType::class,               // L'entité étrangère
            'choice_label' => 'name',                  // Le champ à afficher dans la liste déroulante
            'placeholder' => 'Choisir un type',     // Texte de placeholder (optionnel)
            'label' => 'type',                       // Libellé du champ
            'required' => true,                        // Rendre le champ obligatoire
        ])
        ->add('startAt', null, [
            'label' => 'Début',
            'attr' => ['class' => 'block w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500']
        ])
        ->add('endAt', null, [
            'label' => 'Fin',
            'attr' => ['class' => 'block w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500']
        ])
        ->add('comment', null, [
            'label' => 'Commentaire',
            'attr' => ['class' => 'block w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500']
        ])
        ->add('fichier', FileType::class, [
            'label' => 'Image (fichier PDF, PNG, JPG)',
            'mapped' => false,
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}