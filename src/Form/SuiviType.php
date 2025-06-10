<?php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\Suivi;
use App\Entity\User;
use App\Entity\Veterinaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuiviType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('temperature')
            ->add('rythme_cardiaque')
            ->add('etat')
            ->add('id_client')
            ->add('animal', EntityType::class, [
                'class' => Animal::class,
                'choice_label' => 'nom',
            ])
            ->add('veterinaire', EntityType::class, [
                'class' => Veterinaire::class,
                'choice_label' => 'nom',
            ])

            ->add('id_user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id', 
                'label' => false,
                'disabled' => false, 
'attr' => ['style' => 'display:none;'],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Suivi::class,
        ]);
    }
}
