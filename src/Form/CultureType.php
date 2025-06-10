<?php
namespace App\Form;
use App\Entity\User;
use App\Entity\Culture;
use App\Enum\BensoinsEngrais;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CultureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('surface', NumberType::class, [
                'label' => 'Surface',
                'scale' => 2,
                'required' => true,
            ])
            ->add('date_plantation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de plantation',
                'required' => true,
                'empty_data' => null,
            ])
            ->add('date_recolte', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de récolte',
                'required' => true,
                'empty_data' => null,
            ])
            ->add('region', null, [
                'label' => 'Région',
                'required' => true,
            ])
            ->add('type_culture', null, [
                'label' => 'Type de culture',
                'required' => true,
            ])
            ->add('densite_plantation', NumberType::class, [
                'label' => 'Densité de plantation',
                'scale' => 2,
                'required' => true,
            ])
            ->add('besoins_eau', NumberType::class, [
                'label' => 'Besoins en eau',
                'scale' => 2,
                'required' => true,
            ])
            ->add('besoins_engrais', ChoiceType::class, [
                'choices' => [
                    'Azote' => BensoinsEngrais::AZOTE->value,
                    'Phosphore' => BensoinsEngrais::PHOSPHORE->value,
                    'Potassium' => BensoinsEngrais::POTASSIUM->value,
                    'NPK' => BensoinsEngrais::NPK->value,
                    'Compost' => BensoinsEngrais::COMPOST->value,
                    'Fumier' => BensoinsEngrais::FUMIER->value,
                    'Urée' => BensoinsEngrais::UREE->value,
                ],
                'label' => 'Besoins en engrais',
                'placeholder' => 'Choisir les besoins en engrais',
                'required' => true,
    'empty_data' => BensoinsEngrais::AZOTE->value
            ])
            ->add('rendement_moyen', NumberType::class, [
                'label' => 'Rendement moyen',
                'scale' => 2,
                'required' => true,
            ])
            ->add('cout_moyen', NumberType::class, [
                'label' => 'Coût moyen',
                'scale' => 2,
                'required' => true,
            ])
            ->add('id_user', EntityType::class, [
                'class' => User::class,
                'label' => false,
                'choice_label' => 'id', 
                'disabled' => false, 
'attr' => ['style' => 'display:none;'],
            ]);

       
        $builder->get('besoins_engrais')->addModelTransformer(new class implements DataTransformerInterface {
            public function transform($value)
            {
                
                return $value ? $value->value : null;
            }

            public function reverseTransform($value): ?BensoinsEngrais
            {
               
                if ($value === BensoinsEngrais::AZOTE->value) {
                    return BensoinsEngrais::AZOTE;
                } elseif ($value === BensoinsEngrais::PHOSPHORE->value) {
                    return BensoinsEngrais::PHOSPHORE;
                } elseif ($value === BensoinsEngrais::POTASSIUM->value) {
                    return BensoinsEngrais::POTASSIUM;
                } elseif ($value === BensoinsEngrais::NPK->value) {
                    return BensoinsEngrais::NPK;
                } elseif ($value === BensoinsEngrais::COMPOST->value) {
                    return BensoinsEngrais::COMPOST;
                } elseif ($value === BensoinsEngrais::FUMIER->value) {
                    return BensoinsEngrais::FUMIER;
                } elseif ($value === BensoinsEngrais::UREE->value) {
                    return BensoinsEngrais::UREE;
                }
                return null; 
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Culture::class,
        ]);
    }
}