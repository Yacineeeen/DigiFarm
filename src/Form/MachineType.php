<?php

namespace App\Form;

use App\Entity\Machine;
use App\Entity\User; // Ajouté pour éviter une erreur avec EntityType
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\EtatEquipement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class MachineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('type')
            ->add('date_achat', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'empty_data' => null,
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Actif' => EtatEquipement::ACTIF,
                    'Inactif' => EtatEquipement::INACTIF,
                    'En Maintenance' => EtatEquipement::EN_MAINTENANCE,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('etat_pred')
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Machine::class,
        ]);
    }
}
