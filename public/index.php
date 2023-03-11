<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Trismegiste\Bronze\Core\BusinessApp;

$app = new BusinessApp();

$app->crud('bronze', 'bicycle', function (FormBuilderInterface $builder) {
    return $builder
            ->add('brand', TextType::class)
            ->add('size', TextType::class)
            ->add('color', TextType::class)
            ->add('save', SubmitType::class)
            ->getForm();
});

$app->run();
