<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PasswordChangeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("plainPassword", PasswordType::class, [
                // Instead of being set onto the object directly this is read and encoded in the controller.
                "mapped" => false,
                "attr" => [
                    "autocomplete" => "new-password",
                    "placeholder" => "**********"
                ],
                "label" => "Mot de passe",
                "required" => true,
                "constraints" => [
                    new NotBlank([
                        "message" => "Un mot de passe est requis.",
                    ]),
                    new Length([
                        "min" => 7,
                        "minMessage" => "Le mot de passe doit comporter au moins {{ limit }} caractères",
                        // Max length allowed by Symfony for security reasons.
                        "max" => 4096,
                    ]),
                    new PasswordStrength([
                        "minScore" => 1,
                        "message" =>
                            "Votre mot de passe est trop faible. Minimum deux majuscules ou caractères spéciaux.",
                    ]),
                    new NotCompromisedPassword([
                        "message" => "Ce mot de passe est compromis. Veuillez en choisir un autre.",
                    ]),
                ],
            ])
            ->add("plainPasswordConfirmation", PasswordType::class, [
                "mapped" => false,
                "attr" => [
                    "autocomplete" => "new-password",
                    "placeholder" => "**********"
                ],
                "label" => "Confirmation du mot de passe",
                "required" => true,
                "constraints" => [
                    new NotBlank([
                        "message" => "Veuillez confirmer votre mot de passe.",
                    ]),
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        $form = $context->getRoot();
                        $password = $form->get("plainPassword")->getData();

                        if ($password !== $value) {
                            $context->buildViolation("Les mots de passe ne correspondent pas.")
                                ->addViolation();
                        }
                    }),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
