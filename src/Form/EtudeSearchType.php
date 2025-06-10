<?php

namespace App\Form;

use App\Entity\Expert;
use App\Entity\Etude;
use App\Enum\Climat;
use App\Enum\TypeSol;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudeSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
         
            ->add('climat', ChoiceType::class, [
                'choices' => [
                    'Sec' => Climat::SEC->value,
                    'Humide' => Climat::HUMIDE->value,
                    'Tempéré' => Climat::TEMPERE->value
                ],
                'required' => false,
                'label' => 'Climat',
                'placeholder' => 'Choisir un climat',
                'attr' => ['class' => 'form-select']
            ])
           
            ->add('expert', EntityType::class, [
                'class' => Expert::class,
                'choice_label' => 'nom',
                'required' => false,
                'label' => 'Expert disponible',
                'placeholder' => 'Choisir un expert'
            ])
           ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
