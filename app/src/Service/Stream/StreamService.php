<?php

namespace App\Service\Stream;

use App\Entity\Order;
use App\Entity\Wave;
use App\Form\OrderFormType;
use App\Service\Stream\Contract\StreamInterface;
use App\Service\Stream\DTO\StreamComponent;
use App\Service\Stream\DTO\StreamDirective;
use InvalidArgumentException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Twig\Environment;

class StreamService implements StreamInterface
{
    public function __construct(
        private Environment $twig,
        private FormFactoryInterface $formFactoryInterface
    ) {
    }

    /**
     * @param array<StreamDirective> $directives
     * @return array<string>
     */
    public function renderStreamComponents(string $topic, array $directives): array
    {
        foreach ($directives as $directive) {
            if (!$directive instanceof StreamDirective) {
                throw new InvalidArgumentException('Directive must be an instance of StreamDirective.');
            }
        }

        return array_map(
            fn($directive) => $this->renderComponent($topic, $directive),
            $directives
        );
    }

    public function renderComponent(string $topic, StreamDirective $directive): string
    {
        $component = new StreamComponent($directive->getComponent());

        $templatePath = $this->getTwigTemplatePath($component);
        $action = $directive->getAction()->value;
        $target = $this->getStreamTarget($topic, $directive);
        $context = $directive->getContext();

        if ($component->useForm()) {
            $form = $this->getFormView($component, $directive->getEntity());

            $context = ["{$component->getFormName()}" => $form, ...$context];
        }

        return $this->twig->render(
            $templatePath,
            ["stream_action" => $action, "stream_target" => $target, ...$context]
        );
    }

    private function getStreamTarget(string $topic, StreamDirective $directive): string
    {
        return "stream_{$directive->getTarget()->value}-{$topic}";
    }

    private function getTwigTemplatePath(StreamComponent $component): string
    {
        return sprintf(
            "components/%s/%s/%s.stream.html.twig",
            $component->getType(),
            $component->getName(),
            $component->getName()
        );
    }

    public function getFormView(StreamComponent $component, object $entity): FormView
    {
        [$preparedEntity, $options] = $this->prepareFormEntityAndOptions($component, $entity);

        return $this->formFactoryInterface
            ->create($component->getFormType(), $preparedEntity, $options)
            ->createView();
    }

    public function prepareFormEntityAndOptions(StreamComponent $component, object $entity): array
    {
        if ($component->getFormType() === OrderFormType::class && $entity instanceof Wave) {
            return [new Order(), ['shop' => $entity->getShop()]];
        }

        return [$entity, []];
    }
}
