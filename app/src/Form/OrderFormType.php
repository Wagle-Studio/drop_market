<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Shop;
use App\Entity\Wave;
use App\Repository\WaveRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function __construct(private WaveRepository $waveRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("wave", ChoiceType::class, [
                "label" => "Les trois prochaines dates",
                "choices" => $this->getWaveChoices($options["shop"]),
                "choice_label" => fn($choice) => $choice,
                "expanded" => true,
                "multiple" => false,
            ]);
    }

    /**
     * @return array<int, Wave>
     */
    private function getWaveChoices(Shop $shop): array
    {
        return $this->waveRepository->findNextThreeWavesForShop($shop->getId());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => Order::class,
        ]);

        $resolver->setDefined("shop");
    }
}
