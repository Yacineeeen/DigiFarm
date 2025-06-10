<?php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\Suivi;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Animal1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('type')
            ->add('age')
            ->add('poids')
            ->add('race')
            ->add('suivi', EntityType::class, [
                'class' => Suivi::class,
                'choice_label' => 'id',
                
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
            'data_class' => Animal::class,
        ]);
    }
}
