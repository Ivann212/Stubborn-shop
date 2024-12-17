<?php

namespace App\Form;

use App\Entity\ProductSize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ProductSizeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('size', ChoiceType::class, [
                'choices' => [
                    'XS' => 'XS',
                    'S'  => 'S',
                    'M'  => 'M',
                    'L'  => 'L',
                    'XL' => 'XL',
                ],
                'label' => 'Taille',
                'expanded' => false, // Il ne s'agit pas de boutons radio mais d'une liste déroulante
                'multiple' => false, // Une seule taille par produit
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'attr' => ['min' => 0], // Minimum 0
                'constraints' => [
                    new GreaterThanOrEqual(0), // Validation pour être sûr que le stock est >= 0
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductSize::class,
        ]);
    }
}

