<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                    'Comptable' => 'ROLE_COMPTABLE',
                    // Ajoutez d'autres rôles si nécessaire
                ],
                'multiple' => true, // Permet de sélectionner plusieurs rôles
                'expanded' => true, // Affiche les choix en tant que cases à cocher
            ])
            ->add('login')
            ->add('nom')
            ->add('prenom')
            ->add('adresse')
            ->add('cp')
            ->add('ville')
            ->add('dateEmbauche')
            ->add('oldId')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
