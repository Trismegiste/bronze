<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Translation\Translator;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

/**
 * An app that deals with entities
 */
class BusinessApp extends WebApp
{

    protected $formFactory;

    public function __construct()
    {
        parent::__construct();
        $translator = new Translator('en');

        $this->twig->getLoader()->addPath(__DIR__ . '/../vendor/symfony/twig-bridge/Resources/views/Form');

        $formEngine = new TwigRendererEngine(['form_div_layout.html.twig'], $this->twig);
        $this->twig->addRuntimeLoader(new FactoryRuntimeLoader([
                    FormRenderer::class => function () use ($formEngine) {
                        return new FormRenderer($formEngine);
                    },
        ]));
        $this->twig->addExtension(new TranslationExtension($translator));
        $this->twig->addExtension(new FormExtension());

        $this->formFactory = Forms::createFormFactory();
    }

    protected function createForm(string $fqcn): \Symfony\Component\Form\Form
    {
        return $this->formFactory->create($fqcn);
    }

}
