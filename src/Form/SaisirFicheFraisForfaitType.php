<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisirFicheFraisForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('forfaitEtape', IntegerType::class, [
                'attr' => ['min' => 0,]
            ])
            ->add('fraisKilometrique' , IntegerType::class, [
                'attr' => ['min' => 0,]
            ])
            ->add('nuiteeHotel', IntegerType::class, [
                'attr' => ['min' => 0,]
            ])
            ->add('repasRestaurant', IntegerType::class, [
                'attr' => ['min' => 0,]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
