<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductSize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('price', NumberType::class)
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,  // On précise que ce champ n'est pas directement lié à l'entité Product
            ])
            ->add('isFeatured', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class, ['label' => 'Sauvegarder'])

            // Collection pour les tailles et les stocks
            ->add('sizes', CollectionType::class, [
                'entry_type' => ProductSizeType::class,
                'allow_add' => true,
                'by_reference' => false,
                'label' => 'Tailles et Stocks',
                'prototype' => true,  // Permet d'ajouter dynamiquement des éléments
                'entry_options' => [
                    'label' => false,  // Pas de label pour chaque entrée
                ],
            ]);

        // Ajout d'un écouteur pour le cas où il n'y a pas encore de tailles
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $product = $event->getData();
            $form = $event->getForm();
            if (empty($product->getSizes())) {
                $form->get('sizes')->add(
                    $form->getConfig()->getFormFactory()->createNamed('0', ProductSizeType::class, new ProductSize())
                );
            }
        });

        // Gestion du champ image
        $builder->get('image')->addModelTransformer(new class implements DataTransformerInterface {
            public function transform($value)
            {
                // L'image est déjà un objet File, on la retourne telle quelle
                return $value;
            }

            public function reverseTransform($value)
            {
                // Si une chaîne est passée, créer un objet File
                if (is_string($value)) {
                    return new File($value);
                }
                return $value;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
