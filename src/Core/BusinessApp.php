<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze\Core;

use MongoDB\Driver\Manager;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
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
use Trismegiste\Bronze\MagicEntity;
use Trismegiste\Bronze\PathExtension;
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

    public function __construct(string $env = 'dev')
    {
        parent::__construct($env);
        $translator = new Translator('en');

        $this->twig->getLoader()->getLoaders()[0]->addPath($this->getProjectDir() . '/vendor/symfony/twig-bridge/Resources/views/Form');

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
        $this->twig->addExtension(new PathExtension());

        $ffb = Forms::createFormFactoryBuilder()
                ->addExtension(new ValidatorExtension($validator));
        if ($this->env !== 'test') {
            $ffb->addExtension(new CsrfExtension($csrfTokenManager));
        }
        $this->formFactory = $ffb->getFormFactory();

        $this->mongodb = new Manager('mongodb://localhost:27017');
    }

    /**
     * Creates a Form with its FQCN Type
     * @param string $fqcn
     * @param type $data
     * @param array $options Form options
     * @return Form
     */
    protected function createForm(string $fqcn, $data = null, array $options = []): Form
    {
        return $this->formFactory->create($fqcn, $data, $options);
    }

    /**
     * Registers 2 Routes GET & POST for creating an entity
     * @param string $url
     * @param callable $control A Closure for the controller
     * @return void
     */
    public function form(string $url, callable $control): void
    {
        $this->addRoute($url, $control, ['get', 'post']);
    }

    /**
     * Gets the form builder
     * @param type $data Form data
     * @param array $options Form options
     * @return FormBuilderInterface
     */
    protected function createFormBuilder($data = null, array $options = []): FormBuilderInterface
    {
        return $this->formFactory->createBuilder(FormType::class, $data, $options);
    }

    /**
     * Gets the form builder for a magic form
     * @param string|MagicEntity $data a string for the entity name or an instance of the entity
     * @param array $options Form options
     * @return FormBuilderInterface
     */
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

    /**
     * Gets the MongoDb repository for a given database and a given collection
     * @param string $dbName Database name
     * @param string $collectionName Collection name
     * @return Repository
     */
    protected function getRepository(string $dbName, string $collectionName): Repository
    {
        $fac = new RepositoryFactory($this->mongodb, $dbName);
        return $fac->create($collectionName);
    }

    /**
     * Registers 5 Routes for CRUD operations : show, create, edit, delete and list
     * @param string $dbName Database name
     * @param string $entityName Entity name (and collection name)
     * @param callable $createForm a closure that builds a magic form
     * @return void
     */
    public function crud(string $dbName, string $entityName, callable $createForm): void
    {
        $repo = $this->getRepository($dbName, $entityName);

        // SHOW
        $this->get("/$entityName/{pk}/show", function (string $pk) use ($entityName, $repo) {
            $obj = $repo->load($pk);

            return $this->render("$entityName/show.html.twig", ['entity' => $obj]);
        });

        // CREATE
        $this->form("/$entityName/new/create", function () use ($entityName, $repo, $createForm) {
            $form = $createForm($this->createMagicForm($entityName));

            $form->handleRequest();
            if ($form->isSubmitted() && $form->isValid()) {
                $obj = $form->getData();
                $repo->save($obj);

                return $this->redirectTo("/$entityName/{$obj->getPk()}/edit");
            }

            return $this->render("$entityName/create.html.twig", ['entity_name' => $entityName, 'form' => $form->createView()]);
        });

        // EDIT
        $this->form("/$entityName/{pk}/edit", function (string $pk) use ($entityName, $repo, $createForm) {
            $obj = $repo->load($pk);
            $form = $createForm($this->createMagicForm($obj));

            $form->handleRequest();
            if ($form->isSubmitted() && $form->isValid()) {
                $obj = $form->getData();
                $repo->save($obj);

                return $this->redirectTo("/$entityName/{$obj->getPk()}/show");
            }

            return $this->render("$entityName/edit.html.twig", ['entity_name' => $entityName, 'form' => $form->createView()]);
        });

        // DELETE
        $this->form("/$entityName/{pk}/delete", function (string $pk) use ($entityName, $repo) {
            $obj = $repo->load($pk);
            $form = $this->createMagicForm($obj)
                    ->add('delete', SubmitType::class)
                    ->getForm();

            $form->handleRequest();
            if ($form->isSubmitted() && $form->isValid()) {
                $obj = $form->getData();
                $repo->delete($obj);

                return $this->redirectTo("/$entityName");
            }

            return $this->render("$entityName/delete.html.twig", ['entity_name' => $entityName, 'form' => $form->createView()]);
        });

        // LIST
        $this->get("/$entityName", function () use ($entityName, $repo) {
            $iter = $repo->search();

            return $this->render("$entityName/list.html.twig", ['entity_name' => $entityName, 'listing' => $iter]);
        });
    }

}
