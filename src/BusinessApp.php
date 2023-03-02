<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze;

use MongoDB\Driver\Manager;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Translation\Translator;
use Trismegiste\Strangelove\MongoDb\Repository;
use Trismegiste\Strangelove\MongoDb\RepositoryFactory;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

/**
 * An app that deals with entities
 */
class BusinessApp extends WebApp
{

    protected $formFactory;
    protected $mongodb;

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
        $this->mongodb = new Manager('mongodb://localhost:27017');
    }

    protected function createForm(string $fqcn, $data = null, array $options = []): Form
    {
        return $this->formFactory->create($fqcn, $data, $options);
    }

    public function form(string $url, callable $control): void
    {
        $this->addRoute($url, $control, ['get', 'post']);
    }

    protected function getRepository(string $dbName, string $collectionName): Repository
    {
        $fac = new RepositoryFactory($this->mongodb, $dbName);
        return $fac->create($collectionName);
    }

}
