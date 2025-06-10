<?php

namespace App\Form;

use App\Entity\Abonnement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('idc')
            
        ->add('nom')
        ->add('prenom')    
        
        ->add('numero', TelType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => 'Ex: 12345678']
            ])
            ->add('typeabb', ChoiceType::class, [
                'label' => 'Type d\'abonnement',
                'choices' => [
                    'Bronze (10$/mois)' => 'bronze',
                    'Silver (15$/mois)' => 'silver',
                    'Gold (20$/mois)' => 'gold'
                ],
                'expanded' => true,  // Affichage sous forme de boutons radio
            ])
            ->add('dureeabb', ChoiceType::class, [
                'label' => 'Durée de l\'abonnement',
                'choices' => [
                    1  => 1,
                    6  => 6 ,
                    12  => 12,
                ],
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}
