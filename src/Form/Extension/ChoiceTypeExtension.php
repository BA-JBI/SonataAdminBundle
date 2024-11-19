<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Amine Zaghdoudi <amine.zaghdoudi@ekino.com>
 *
 * @psalm-suppress MissingTemplateParam https://github.com/phpstan/phpstan-symfony/issues/320
 */
final class ChoiceTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple'] && (true === ($options['sortable'] ?? false))) {
            $builder->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $event) use ($options) {
                /** @var PreSubmitEvent $event */
                $form = $event->getForm();
                $data = $event->getData();

                if (!is_array($data) || count($data) !== 1) {
                    return;
                }

                if (str_contains($data[0], ',')) {
                    $event->setData(explode(',', $data[0]));
                }
            }, 1);
        }
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $optionalOptions = ['sortable'];

        $resolver->setDefined($optionalOptions);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['sortable'] = true === ($options['sortable'] ?? false);
    }

    /**
     * @return string[]
     *
     * @phpstan-return class-string<FormTypeInterface>[]
     */
    public static function getExtendedTypes(): iterable
    {
        return [ChoiceType::class];
    }
}
