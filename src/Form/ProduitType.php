<?php
namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('type', TextType::class, [
            'required' => true,
            'label' => 'Type de produit',
            ])
            ->add('reference', TextType::class, [
                'label' => 'Référence'
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix'
            ])
            ->add('description', TextType::class, [
                'label' => 'Description'
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Stock'
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false, // Important : car ce champ n'existe pas dans l'entité
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, WEBP).',
                    ]),
                ],
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
            'data_class' => Produit::class,
        ]);
    }
}

