<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("firstname", TextType::class, [
                "label" =>  "Prénom",
                "attr" => [
                    "placeholder" => "Dwight"
                ]
            ])
            ->add("lastname", TextType::class, [
                "label" =>  "Nom",
                "attr" => [
                    "placeholder" => "Schrute"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => User::class,
        ]);
    }
}
