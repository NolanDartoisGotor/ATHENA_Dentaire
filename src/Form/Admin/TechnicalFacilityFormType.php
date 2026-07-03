<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\TechnicalFacility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

final class TechnicalFacilityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('description', TextareaType::class, ['label' => 'Description', 'attr' => ['rows' => 4]])
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Veuillez sélectionner une image JPEG, PNG ou WebP.',
                    ),
                ],
            ])
            ->add('layout', ChoiceType::class, [
                'label' => 'Mise en page',
                'choices' => [
                    'Grande carte' => 'large',
                    'Carte standard' => 'standard',
                ],
            ])
            ->add('position', IntegerType::class, ['label' => 'Position'])
            ->add('published', CheckboxType::class, ['label' => 'Publié', 'required' => false])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TechnicalFacility::class,
        ]);
    }
}
