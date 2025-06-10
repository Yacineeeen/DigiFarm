<?php
namespace App\Form;

use App\Entity\Etude;
use App\Entity\Culture;
use App\Entity\Expert;
use App\Enum\Climat;
use App\Entity\User;
use App\Enum\TypeSol;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\CallbackTransformer;
use Doctrine\ORM\EntityRepository ;
class EtudeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_r', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de l\'étude',
                'empty_data' => null,
            ])
            ->add('culture', EntityType::class, [
                'class' => Culture::class,
                'choice_label' => 'nom',  
                'label' => 'Culture',
            ])
            ->add('expert', EntityType::class, [
                'class' => Expert::class,
                'choice_label' => 'nom',  
                'label' => 'Expert',
                'query_builder' => function(EntityRepository $er) {
        return $er->findAvailableExperts();
    },
            ])
            ->add('climat', ChoiceType::class, [
                'choices' => [
                    'Sec' => Climat::SEC->value,
                    'Humide' => Climat::HUMIDE->value,
                    'Tempéré' => Climat::TEMPERE->value,
                ],
                'label' => 'Climat',
                'placeholder' => 'Choisir le climat',
                'required' => true,
            ])
            ->add('type_sol', ChoiceType::class, [
                'choices' => [
                    'ARGILEUX' => TypeSol::ARGILEUX->value,
                    'SABLEUX' => TypeSol::SABLEUX->value,
                    'LIMONEUX' => TypeSol::LIMONEUX->value,
                ],
                'label' => 'Type de sol',
                'placeholder' => 'Choisir le type de sol',
                'required' => true,
            ])
            ->add('irrigation', CheckboxType::class, [
                'label' => 'Irrigation',
                'required' => false,
            ])
            ->add('fertilisation', CheckboxType::class, [
                'label' => 'Fertilisation',
                'required' => false,
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
                'scale' => 2,
                'required' => true,
            ])
            ->add('rendement', NumberType::class, [
                'label' => 'Rendement',
                'scale' => 2,
                'required' => true,
            ])
            ->add('precipitations', NumberType::class, [
                'label' => 'Précipitations',
                'scale' => 2,
                'required' => true,
            ])
            ->add('main_oeuvre', NumberType::class, [
                'label' => 'Main d\'œuvre',
                'scale' => 2,
                'required' => true,
            ])
            ->add('id_user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id', 
                'label' => false,
                'disabled' => false, 
'attr' => ['style' => 'display:none;'],
            ]);

       
        $builder->get('climat')
            ->addModelTransformer(new CallbackTransformer(
                fn ($climat) => $climat instanceof Climat ? $climat->value : null,
                fn ($value) => is_string($value) || is_int($value) ? Climat::tryFrom($value) : null
            ));

        
        $builder->get('type_sol')
            ->addModelTransformer(new CallbackTransformer(
                fn ($typeSol) => $typeSol instanceof TypeSol ? $typeSol->value : null,
                fn ($value) => is_string($value) || is_int($value) ? TypeSol::tryFrom($value) : null
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etude::class, 
            'compound' => true, 
        ]);
    }
}
