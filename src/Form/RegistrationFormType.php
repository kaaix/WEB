<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', TextType::class, [
                'label' => 'Identifiant',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un identifiant',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 180,
                        'minMessage' => 'Votre identifiant doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre identifiant ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom']),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom']),
                ],
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre date de naissance']),
                ],
            ])
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez un pays',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un pays']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est requis']),
                    new Length([
                        'min' => 6,
                        'max' => 4096,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre mot de passe ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J’accepte les conditions générales',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
