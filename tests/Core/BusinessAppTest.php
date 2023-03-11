<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Tests\Trismegiste\Bronze\Core\AppTestCase;
use Trismegiste\Bronze\Core\App;
use Trismegiste\Bronze\Core\BusinessApp;

class BusinessAppTest extends AppTestCase
{

    protected function createApp(): App
    {
        return new BusinessApp('test');
    }

    public function testForm()
    {
        $this->sut->form('/entity', function () {
            $form = $this->createFormBuilder()
                    ->add('firstname', TextType::class)
                    ->add('save', SubmitType::class)
                    ->getForm();

            return $this->render('bicycle/form.html.twig', ['form' => $form->createView()]);
        });

        $crawler = $this->client->request('GET', '/entity');
        $this->assertStatusCodeEquals(200);
        $this->assertResponseContainsString('<form');

        $form = $crawler->selectButton('form[save]')->form();
        $this->client->submit($form, ['form[firstname]' => 'Motoko']);
        $this->assertStatusCodeEquals(200);
    }

    public function testCrud()
    {
        $this->sut->crud('bronze', 'bicycle', function (FormBuilderInterface $builder) {
            return $builder
                    ->add('name', TextType::class)
                    ->add('save', SubmitType::class)
                    ->getForm();
        });

        $this->client->request('GET', '/bicycle');
        $this->assertStatusCodeEquals(200);
        $this->client->request('GET', '/bicycle/new/create');
        $this->assertStatusCodeEquals(200);
    }

}
