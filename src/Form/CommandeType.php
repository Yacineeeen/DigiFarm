<?php


namespace App\Form;
use App\Entity\Commande;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $disabled = $options['disabled'] ?? false; // Vérifie si l'option est définie

        $builder
        ->add('produit', EntityType::class, [
            'class' => Produit::class,
            'choice_label' => 'nom',
            'label' => 'Produit',
            'attr' => ['class' => 'form-control'],
        ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'en_cours',
                    'Validée' => 'validée',
                    'Livrée' => 'livrée',
                    'Annulée' => 'annulée',
                ],
                'label' => 'Statut',
                'disabled' => $disabled,
                'attr' => ['class' => 'form-control'],
            ])

            ->add('quantite', NumberType::class, [
                'label' => 'Quantité',
                'disabled' => $disabled,
                'attr' => ['class' => 'form-control'],
            ])
    
            ->add('montantTotal', NumberType::class, [
                'label' => 'Montant Total',
                'disabled' => $disabled, 
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateCommande', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de Commande',
                'disabled' => $disabled,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'disabled' => false, // Définit une valeur par défaut pour éviter l'erreur
        ]);
    }
}