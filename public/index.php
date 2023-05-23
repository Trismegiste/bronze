<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Trismegiste\Bronze\Core\BusinessApp;
use Trismegiste\Bronze\Entity\WikiPage;
use Trismegiste\Bronze\Wikitext\InternalDataAccess;
use Trismegiste\Bronze\Wikitext\InternalSiteConfig;
use Wikimedia\Parsoid\Mocks\MockDataAccess;
use Wikimedia\Parsoid\Mocks\MockPageConfig;
use Wikimedia\Parsoid\Mocks\MockPageContent;
use Wikimedia\Parsoid\Parsoid;

$app = new BusinessApp();

$app->form('/parse', function () {
    /** @var Form $form */
    $form = $this->createFormBuilder()
            ->add('wikitext', TextareaType::class, ['attr' => ['rows' => 10]])
            ->add('parse', SubmitType::class)
            ->getForm();

    $parsed = '';
    $form->handleRequest();
    if ($form->isSubmitted() && $form->isValid()) {
        $opts = [];

        $parserOpts = [
            'body_only' => true,
            'wrapSections' => false,
        ];

        $siteConfig = new InternalSiteConfig($opts);
        $dataAccess = new MockDataAccess($opts);
        $parsoid = new Parsoid($siteConfig, $dataAccess);

        $pageContent = new MockPageContent(['main' => $form->getData()['wikitext']]);
        $pageConfig = new MockPageConfig($opts, $pageContent);
        $parsed = $parsoid->wikitext2html($pageConfig, $parserOpts);
    }

    return $this->render('parser/form.html.twig', ['form' => $form->createView(), 'parsed' => $parsed]);
});

$app->form('/create', function () {
    $repo = $this->getRepository('wekissmedia', 'page');

    /** @var Form $form */
    $form = $this->createFormBuilder(null, ['data_class' => WikiPage::class])
            ->add('title', TextType::class)
            ->add('content', TextareaType::class, ['attr' => ['rows' => 10]])
            ->add('parse', SubmitType::class)
            ->getForm();

    $parsed = '';
    $form->handleRequest();
    if ($form->isSubmitted() && $form->isValid()) {
        $opts = [];

        $parserOpts = [
            'body_only' => true,
            'wrapSections' => false,
        ];

        $siteConfig = new InternalSiteConfig($opts);
        $dataAccess = new InternalDataAccess($repo);
        $parsoid = new Parsoid($siteConfig, $dataAccess);

        $page = $form->getData();
        $pageContent = new MockPageContent(['main' => $page->content]);
        $pageConfig = new MockPageConfig($opts, $pageContent);
        $parsed = $parsoid->wikitext2html($pageConfig, $parserOpts);

        $repo->save($page);
    }

    return $this->render('parser/form.html.twig', ['form' => $form->createView(), 'parsed' => $parsed]);
});

$app->get('/show/{title}', function (string $title) {
    $repo = $this->getRepository('wekissmedia', 'page');

    $page = $repo->searchOne(['title' => $title]);

    $opts = [];
    $parserOpts = [
        'body_only' => true,
        'wrapSections' => false,
    ];

    $siteConfig = new InternalSiteConfig($opts);
    $dataAccess = new InternalDataAccess($repo);
    $parsoid = new Parsoid($siteConfig, $dataAccess);

    $pageContent = new MockPageContent(['main' => $page->content]);
    $pageConfig = new MockPageConfig($opts, $pageContent);
    $parsed = $parsoid->wikitext2html($pageConfig, $parserOpts);

    return $this->render('parser/show.html.twig', ['title' => $title, 'parsed' => $parsed]);
});

$app->run();
