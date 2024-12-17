<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('email', EmailType::class, [
        'constraints' => [
            new NotBlank(['message' => "L'email ne peut pas être vide."]),
            new Email(['message' => 'Veuillez fournir un email valide.']),
        ],
        'label' => 'Adresse Email',
    ])
    ->add('password', PasswordType::class, [
        'constraints' => [
            new NotBlank(['message' => 'Le mot de passe ne peut pas être vide.']),
        ],
        'label' => 'Mot de passe',
    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'authenticate',
        ]);
    }
}
