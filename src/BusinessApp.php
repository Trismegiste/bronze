<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze;

use MongoDB\Driver\Manager;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;
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

        $this->twig->getLoader()->addPath($this->getProjectDir() . '/vendor/symfony/twig-bridge/Resources/views/Form');

        // copy-pasted from https://github.com/xmgcoyi/standalone-forms/blob/4.2+twig/src/setup.php
        // CSRF
        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new NativeSessionTokenStorage();
        $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);
        $csrfTokenManager = new CsrfTokenManager();

        // Set up the Validator component
        $validator = Validation::createValidator();

        $formEngine = new TwigRendererEngine(['form_div_layout.html.twig'], $this->twig);
        $this->twig->addRuntimeLoader(new FactoryRuntimeLoader([
                    FormRenderer::class => function () use ($formEngine, $csrfManager) {
                        return new FormRenderer($formEngine, $csrfManager);
                    },
        ]));
        $this->twig->addExtension(new TranslationExtension($translator));
        $this->twig->addExtension(new FormExtension());

        $this->formFactory = Forms::createFormFactoryBuilder()
                // ->addExtension(new CsrfExtension($csrfTokenManager))
                ->addExtension(new ValidatorExtension($validator))
                ->getFormFactory();
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

    protected function createFormBuilder($data = null, array $options = []): FormBuilderInterface
    {
        return $this->formFactory->createBuilder(FormType::class, $data, $options);
    }

    protected function createMagicForm($data, array $options = []): FormBuilderInterface
    {
        $options['data_class'] = MagicEntity::class;

        if ($data instanceof MagicEntity) {
            $fac = $this->formFactory->createBuilder(FormType::class, $data, $options);
        }

        if (is_string($data)) {
            $fac = $this->formFactory->createBuilder(FormType::class, null, $options)
                    ->add('__entity', HiddenType::class, ['data' => $data]);
        }

        return $fac;
    }

    protected function getRepository(string $dbName, string $collectionName): Repository
    {
        $fac = new RepositoryFactory($this->mongodb, $dbName);
        return $fac->create($collectionName);
    }

}
