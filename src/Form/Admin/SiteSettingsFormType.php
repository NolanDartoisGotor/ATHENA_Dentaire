<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\SiteSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SiteSettingsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('organizationName', TextType::class, ['label' => 'Nom du cabinet'])
            ->add('tagline', TextType::class, ['label' => 'Accroche'])
            ->add('address', TextType::class, ['label' => 'Adresse'])
            ->add('phone', TextType::class, ['label' => 'Téléphone'])
            ->add('emergencyPhone', TextType::class, ['label' => 'Téléphone urgences'])
            ->add('contactEmail', EmailType::class, ['label' => 'Email de contact'])
            ->add('formRecipientEmail', EmailType::class, ['label' => 'Email destinataire formulaire', 'required' => false])
            ->add('googleMapsEmbedUrl', UrlType::class, ['label' => 'URL Google Maps embed', 'required' => false])
            ->add('heroImage', TextType::class, ['label' => 'Image hero (chemin)', 'required' => false])
            ->add('mapImage', TextType::class, ['label' => 'Image carte (chemin)', 'required' => false])
            ->add('accessPlanImage', TextType::class, ['label' => 'Plan d\'accès (chemin)', 'required' => false])
            ->add('assistantsList', TextareaType::class, ['label' => 'Liste des assistantes', 'required' => false])
            ->add('contactFormNotice', TextareaType::class, ['label' => 'Notice formulaire', 'required' => false])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SiteSettings::class,
        ]);
    }
}
