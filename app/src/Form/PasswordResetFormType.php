<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordResetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("email", EmailType::class, [
            "label" => "Adresse email",
            "required" => true,
            "attr" => [
                "placeholder" => "exemple@gmail.com"
            ],
            "constraints" => [
                new NotBlank([
                    'message' => "Champs email requis.",
                ]),
                new Email([
                    'message' => "Veuillez entrer une adresse email valide.",
                ]),
                new Length([
                    'max' => 100,
                    'maxMessage' => "L'adresse email ne peut pas dépasser {{ limit }} caractères.",
                ]),
                new Length([
                    'min' => 5,
                    'maxMessage' => "L'adresse email ne peut pas faire moins de {{ limit }} caractères.",
                ]),
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
