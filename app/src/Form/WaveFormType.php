<?php

namespace App\Form;

use App\Entity\Wave;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WaveFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("start", DateTimeType::class, [
                "label" => "Date et heure du créneau",
                "widget" => "single_text",
                "html5" => true,
                "input" => "datetime",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => Wave::class,
        ]);
    }
}
