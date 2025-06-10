<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Définition de tous les rôles
        $choices = [
            'Administrateur' => UserRole::ADMIN,
            'Agriculteur'   => UserRole::AGRICULTEUR,
            'Client'        => UserRole::CLIENT,
        ];

        // Si l'option include_admin est false, on retire l'administrateur
        if (!$options['include_admin']) {
            unset($choices['Administrateur']);
        }

        $builder
            ->add('Nom')
            ->add('Prenom')
            ->add('AdresseMail')
            ->add('Password', PasswordType::class)
            ->add('Role', ChoiceType::class, [
                'choices'      => $choices,
                // Conversion de l'énum en chaîne de caractères
                'choice_value' => fn (?UserRole $role) => $role?->value,
                // Affichage du nom de l'énum dans le formulaire
                'choice_label' => fn (UserRole $role) => $role->name,
                'expanded'     => false,
                'multiple'     => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => User::class,
            // Option personnalisée pour inclure ou non l'administrateur
            'include_admin' => true,
        ]);
    }
}
