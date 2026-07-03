<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\PageSection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageSectionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isLegalPage = (bool) $options['is_legal_page'];

        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('subtitle', TextType::class, ['label' => 'Sous-titre', 'required' => false])
            ->add('body', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['rows' => $isLegalPage ? 20 : 8],
                'required' => false,
                'help' => $isLegalPage
                    ? 'Le HTML basique est supporté : <p>, <strong>, <ul>, <li>, <a>, <h2>.'
                    : null,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageSection::class,
            'is_legal_page' => false,
        ]);

        $resolver->setAllowedTypes('is_legal_page', 'bool');
    }
}
