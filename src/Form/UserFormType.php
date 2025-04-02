<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Pays;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;

class UserFormType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        $currentRoles = $currentUser ? $currentUser->getRoles() : [];

        $roleChoices = [];

        // 🔒 Règles du sujet :
        if (in_array('ROLE_ADMIN', $currentRoles)) {
            $roleChoices = ['Client' => 'ROLE_CLIENT'];
        } elseif (in_array('ROLE_SUPER_ADMIN', $currentRoles)) {
            $roleChoices = ['Admin' => 'ROLE_ADMIN'];
        }

        $builder
            ->add('login')
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'Mot de passe',
            ])
            ->add('nom')
            ->add('prenom')
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de naissance',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => $roleChoices,
                'multiple' => true, // Important : pas plusieurs rôles à la fois
                'expanded' => true,
            ])
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'nom',
                'label' => 'Pays',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
