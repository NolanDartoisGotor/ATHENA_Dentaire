<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\OpeningHour;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OpeningHourFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dayLabel', TextType::class, ['label' => 'Jour(s)'])
            ->add('hours', TextType::class, ['label' => 'Horaires'])
            ->add('position', IntegerType::class, ['label' => 'Position'])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OpeningHour::class,
        ]);
    }
}
