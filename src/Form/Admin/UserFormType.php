<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('fullName', TextType::class, ['label' => 'Nom complet'])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Administrateur' => User::ROLE_ADMIN,
                    'Éditeur' => User::ROLE_EDITOR,
                ],
                'multiple' => true,
                'constraints' => [new Count(min: 1, minMessage: 'Choisissez au moins un rôle.')],
            ])
            ->add('emailAuthEnabled', CheckboxType::class, [
                'label' => 'Double authentification par email',
                'help' => 'Toujours active pour les administrateurs.',
                'required' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'required' => $options['is_new'],
                'constraints' => [
                    ...($options['is_new'] ? [new NotBlank()] : []),
                    new Length(min: 12, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'),
                    new PasswordStrength(minScore: PasswordStrength::STRENGTH_MEDIUM, message: 'Mot de passe trop faible.'),
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_new' => false,
        ]);
    }
}
