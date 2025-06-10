<?php

namespace App\Form;

use App\Entity\commande;
use App\Entity\produit;
use App\Entity\CommandeDetail;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prixUnitaire', MoneyType::class, [
                'currency' => 'EUR',
            ])
            ->add('montantTotal', MoneyType::class, [
                'currency' => 'EUR',
            ])
            ->add('commande', EntityType::class, [
                'class' => Commande::class,
                'choice_label' => 'id',
            ])
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommandeDetail::class,
        ]);
    }
}
